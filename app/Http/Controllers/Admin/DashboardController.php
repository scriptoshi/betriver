<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\User as ResourcesUser;
use App\Models\Game;
use App\Models\Stake;
use App\Models\Ticket;
use App\Models\Trade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with various statistics.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $houseWins = Ticket::where('status', TicketStatus::LOSER)->sum('amount');
        $houseLosses = Ticket::where('status', TicketStatus::LOSER)->sum('payout');

        return Inertia::render('Admin/Dashboard', [
            'totalUsers' => fn() => User::count(),
            'totalTickets' => fn() => Ticket::count(),
            'totalSlips' => fn() => Stake::count(),
            'usersRegisteredToday' => [
                'value' => fn() => $this->getTodayCount(User::class),
                'percentage' => fn() => $this->getPercentageChange(User::class),
            ],
            'activeBettors' => [
                'value' => fn() => $this->getActiveUsers(),
                'percentage' => fn() => $this->getPercentageChangeActiveUsers(),
            ],
            'liveGamesToday' => [
                'value' => fn() => $this->getTodayCount(Game::class, 'startTime'),
                'percentage' => fn() => $this->getPercentageChange(Game::class, 'startTime'),
            ],
            'commissionCollected' => [
                'value' => fn() => $this->getTodaySum(Trade::class, 'commission'),
                'percentage' => fn() => $this->getPercentageChangeSum(Trade::class, 'commission'),
            ],
            'arbitrage' => fn() => Trade::sum('margin'),
            'houseWins' => fn() => $houseWins,
            'losses' => fn() => $houseLosses,
            'profit' => fn() => $houseWins - $houseLosses,
            'totalBookieBets' => fn() => Ticket::sum('amount'),
            'totalExchangeBets' => fn() => Stake::sum('amount'),
            'chartData' => $this->generateAdminEarningsData($request),
            'latest' => ResourcesUser::collection(User::query()->latest()->take(10)->get())
        ]);
    }

    /**
     * Get the count of records created today for a given model.
     *
     * @param string $model The fully qualified class name of the model
     * @param string $dateField The name of the date field to query (default: 'created_at')
     * @return int
     */
    private function getTodayCount($model, $dateField = 'created_at')
    {
        return $model::whereDate($dateField, today())->count();
    }

    /**
     * Get the count of records created yesterday for a given model.
     *
     * @param string $model The fully qualified class name of the model
     * @param string $dateField The name of the date field to query (default: 'created_at')
     * @return int
     */
    private function getYesterdayCount($model, $dateField = 'created_at')
    {
        return $model::whereDate($dateField, Carbon::yesterday())->count();
    }

    /**
     * Get the sum of a field for records created today for a given model.
     *
     * @param string $model The fully qualified class name of the model
     * @param string $field The name of the field to sum
     * @return float
     */
    private function getTodaySum($model, $field)
    {
        return $model::whereDate('created_at', today())->sum($field);
    }

    /**
     * Get the sum of a field for records created yesterday for a given model.
     *
     * @param string $model The fully qualified class name of the model
     * @param string $field The name of the field to sum
     * @return float
     */
    private function getYesterdaySum($model, $field)
    {
        return $model::whereDate('created_at', Carbon::yesterday())->sum($field);
    }

    /**
     * Calculate the percentage change between today's and yesterday's count for a given model.
     *
     * @param string $model The fully qualified class name of the model
     * @param string $dateField The name of the date field to query (default: 'created_at')
     * @return float
     */
    private function getPercentageChange($model, $dateField = 'created_at')
    {
        $todayCount = $this->getTodayCount($model, $dateField);
        $yesterdayCount = $this->getYesterdayCount($model, $dateField);
        return $this->calculatePercentageChange($todayCount, $yesterdayCount);
    }

    /**
     * Calculate the percentage change between today's and yesterday's sum for a given model and field.
     *
     * @param string $model The fully qualified class name of the model
     * @param string $field The name of the field to sum
     * @return float
     */
    private function getPercentageChangeSum($model, $field)
    {
        $todaySum = $this->getTodaySum($model, $field);
        $yesterdaySum = $this->getYesterdaySum($model, $field);
        return $this->calculatePercentageChange($todaySum, $yesterdaySum);
    }

    /**
     * Calculate the percentage change between two values.
     *
     * @param float $today The current value
     * @param float $yesterday The previous value
     * @return float
     */
    private function calculatePercentageChange($today, $yesterday)
    {
        if ($yesterday == 0) return $today > 0 ? 100 : 0;
        return round((($today - $yesterday) / $yesterday) * 100, 1);
    }

    /**
     * Get the count of active users (users who placed a stake today).
     *
     * @return int
     */
    private function getActiveUsers()
    {
        return User::whereHas('stakes', function ($query) {
            $query->whereDate('created_at', today());
        })->count();
    }

    /**
     * Calculate the percentage change in active users between today and yesterday.
     *
     * @return float
     */
    private function getPercentageChangeActiveUsers()
    {
        $todayActive = $this->getActiveUsers();
        $yesterdayActive = User::whereHas('stakes', function ($query) {
            $query->whereDate('created_at', Carbon::yesterday());
        })->count();
        return $this->calculatePercentageChange($todayActive, $yesterdayActive);
    }

    /**
     * Generate data for the chart.
     *
     * @return float
     */


    private function generateAdminEarningsData(Request $request)
    {
        $period = $request->get('chart', '7days');
        $endDate = Carbon::now();
        $startDate = match ($period) {
            '1day' => $endDate->copy()->subDay(),
            '7days' => $endDate->copy()->subDays(7),
            '1month' => $endDate->copy()->subMonth(),
            '6months' => $endDate->copy()->subMonths(6),
            '1year' => $endDate->copy()->subYear(),
            'lifetime' => Carbon::parse(Ticket::min('created_at')),
            default => $endDate->copy()->subDays(7),
        };

        $groupBy = match ($period) {
            '1day' => "DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')",
            '7days', '1month' => "DATE(created_at)",
            '6months', '1year' => "DATE_FORMAT(created_at, '%Y-%m-01')",
            'lifetime' => "DATE_FORMAT(created_at, '%Y-%m-01')",
            default => "DATE(created_at)",
        };

        $data = DB::table('tickets')
            ->select(DB::raw("
            {$groupBy} as date,
            SUM(CASE WHEN status = 'loser' THEN amount ELSE 0 END) as house_wins,
            SUM(CASE WHEN status = 'loser' THEN payout ELSE 0 END) as house_losses
        "))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw($groupBy))
            ->orderBy('date')
            ->get();

        $arbitrageData = DB::table('trades')
            ->select(DB::raw("
            {$groupBy} as date,
            SUM(margin) as arbitrage
        "))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw($groupBy))
            ->orderBy('date')
            ->get();

        $commissionData = DB::table('trades')
            ->select(DB::raw("
            {$groupBy} as date,
            SUM(commission) as commission
        "))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw($groupBy))
            ->orderBy('date')
            ->get();

        $result = [];
        foreach ($data as $row) {
            $date = Carbon::parse($row->date)->format('Y-m-d');
            $result[$date] = [
                'date' => $date,
                'houseWins' => $row->house_wins,
                'houseLosses' => $row->house_losses,
                'arbitrage' => 0,
                'commission' => 0,
            ];
        }

        foreach ($arbitrageData as $row) {
            $date = Carbon::parse($row->date)->format('Y-m-d');
            if (isset($result[$date])) {
                $result[$date]['arbitrage'] = $row->arbitrage;
            }
        }

        foreach ($commissionData as $row) {
            $date = Carbon::parse($row->date)->format('Y-m-d');
            if (isset($result[$date])) {
                $result[$date]['commission'] = $row->commission;
            }
        }

        return array_values($result);
    }
}
