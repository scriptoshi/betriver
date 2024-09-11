<?php

namespace App\Http\Controllers;

use App\Actions\Uploads;
use App\Enums\LeagueSport;
use App\Enums\PersonalBetEmails;
use App\Enums\PersonalLossLimitInterval;
use App\Enums\PersonalProofOfAddressType;
use App\Enums\PersonalProofOfIdentityType;
use App\Enums\StakeType;
use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Enums\UserLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\Personal;
use App\Http\Resources\Transaction as TransactionResource;
use App\Models\Stake;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Commission;
use App\Models\Feedback;
use App\Models\Ticket;
use App\Models\Wager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class UsersController extends Controller
{
    /**
     * Show the users statement.
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function statement(Request $request)
    {
        $keyword = $request->get('search');
        $types = str($request->get('types', ''))
            ->explode(',')
            ->map(fn($str) => trim($str))
            ->filter(fn($val) => TransactionType::tryFrom($val) !== null);
        $sports = str($request->get('sports', ''))
            ->explode(',')
            ->map(fn($str) => trim($str))
            ->filter(fn($val) => LeagueSport::tryFrom($val) !== null);
        $time = $request->get('time', null);
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $credit = $request->get('credit', null);
        $debit = $request->get('debit', null);
        $perPage = 10;
        $query  = $request->user()->transactions()
            ->with('transactable');

        if (!empty($keyword)) {
            $query->where('description', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('action', 'LIKE', "%$keyword%")
                ->orWhere('type', 'LIKE', "%$keyword%");
        }
        if ($sports->count()) {
            $query->whereHasMorph('transactable', [
                Stake::class,
                Wager::class
            ], function (Builder $query) use ($sports) {
                $query->whereIn('sport', $sports->all());
            });
        }
        if ($types->count()) {
            $query->whereIn('type', $types->all());
        }

        if ($debit !== null) {
            $query->where('action', TransactionAction::DEBIT);
        }

        if ($credit !== null) {
            $query->where('action', TransactionAction::CREDIT);
        }

        if ($time !== null) {
            $daysAgo = intval($time);
            $query->where('created_at', '>=', now()->subDays($daysAgo));
        } elseif ($from !== null && $to !== null) {
            $fromDate = Carbon::createFromTimestamp($from);
            $toDate = Carbon::createFromTimestamp($to);
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }
        return Inertia::render('Account/Statement', [
            'transactions' => function () use ($query, $perPage) {
                $transactionsItems = $query->clone()
                    ->latest('created_at')
                    ->paginate($perPage)
                    ->loadMorph('transactable', [
                        Ticket::class => ['wagers.game'],
                        Stake::class => ['game'],
                    ]);
                return  TransactionResource::collection($transactionsItems);
            },
            'won' => function () use ($query) {
                return $query->clone()
                    ->where('type', TransactionType::WIN)
                    ->count();
            },
            'lost' => function () use ($query) {
                return $query->clone()
                    ->where('type', TransactionType::BET)
                    ->whereHasMorph('transactable', [
                        Stake::class,
                        Wager::class
                    ], fn(Builder $query) => $query->where('won', false))
                    ->count();
            },
            'bets' => function () use ($query) {
                return $query->clone()->where('type', TransactionType::BET)->count();
            },
            'profitLoss' => function () use ($query) {
                // cant get to loss stakes !!
                return $query->clone()
                    ->where('type', TransactionType::TRADE_OUT_PROFIT)
                    ->sum('amount');
            },
            'exposure' => function () use ($query) {
                return $query->clone()
                    ->where('type', TransactionType::BET)
                    ->sum('amount');
            },
            'referrals' => function () use ($request) {
                return User::where('referral', 'LIKE', $request->user()->refId)->count();
            },
            'refComMonth' => function () use ($request) {
                return $request->user()
                    ->transactions()
                    ->where('type', TransactionType::REFERRAL_COMMISSION)
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('amount');
            },
            'refComLifetime' => function () use ($request) {
                return $request->user()
                    ->transactions()
                    ->where('type', TransactionType::REFERRAL_COMMISSION)
                    ->sum('amount');
            },
            'typeOptions' => TransactionType::getNames(),
            'sportsOptions' => LeagueSport::getNames(),
        ]);
    }

    /**
     * Show the users referrals page.
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function referrals(Request $request)
    {

        return Inertia::render(
            'Account/Referrals',
            [
                'direct' => User::where('referral', 'LIKE', $request->user()->refId . "%")
                    ->count(),
                'referrals' => [
                    'info' => __('Referrals :month', ['month' => now()->format('M Y')]),
                    'stat' => User::where('referral', 'LIKE', "%" . $request->user()->refId . "%")
                        ->where('created_at', '>=', now()->startOfMonth())
                        ->count() ?? 0,
                    'last_month' => User::where('referral', 'LIKE', "%" . $request->user()->refId . "%")
                        ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])
                        ->count() ?? 0,
                ],
                'earnings' => [
                    'info' => __('Total Earned :month', ['month' => now()->format('M Y')]),
                    'stat' => $request->user()
                        ->transactions()
                        ->where('type', TransactionType::REFERRAL_COMMISSION)
                        ->where('created_at', '>=', now()->startOfMonth())
                        ->sum('amount') ?? 0,
                    'last_month' => $request->user()
                        ->transactions()
                        ->where('type', TransactionType::REFERRAL_COMMISSION)
                        ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])
                        ->sum('amount') ?? 0,
                ],
                'lifeTime' => [
                    'info' => __('Since :year', ['year' => now()->subYear()->format('M Y')]),
                    'stat' => $request->user()
                        ->transactions()
                        ->where('created_at', '>=', now()->subYear())
                        ->where('type', TransactionType::REFERRAL_COMMISSION)
                        ->sum('amount') ?? 0
                ],

                'levels' => Commission::query()->where('active', true)
                    ->get()
                    ->groupBy('level')
                    ->values(),

            ]
        );
    }

    /**
     * Show the users settings page.
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function settings(Request $request)
    {
        return Inertia::render('Account/Settings', [
            'personal' => new Personal($request->user()->personal()->firstOrCreate([])),
        ]);
    }

    /**
     * Shows the users settings page.
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function limits(Request $request)
    {
        return Inertia::render('Account/Limits', [
            'lossOptions' => PersonalLossLimitInterval::getNames(),
            'mailOptions' => PersonalBetEmails::getNames(),
            'personal' => new Personal($request->user()->personal()->firstOrCreate([])),
        ]);
    }

    /**
     * Shows the users settings page.
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function alerts(Request $request)
    {
        return Inertia::render('Account/Alerts', [
            'mailOptions' => PersonalBetEmails::getNames(),
            'personal' => new Personal($request->user()->personal()->firstOrCreate([])),
        ]);
    }
    /**
     * Show the users kyc page.
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function verify(Request $request)
    {
        return Inertia::render('Account/Verification', [
            'personal' => new Personal($request->user()->personal()->firstOrCreate([])),
            'idTypes' => PersonalProofOfIdentityType::getNames(),
            'addressTypes' => PersonalProofOfAddressType::getNames(),
        ]);
    }


    /**
     * Show the users commission page.
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function commission(Request $request)
    {
        $query = Transaction::query()
            ->whereHasMorph('transactable', [
                Stake::class,
                Wager::class
            ]);
        return Inertia::render('Account/Level', [
            'levelOne' => UserLevel::ONE->config(),
            'levelTwo' => UserLevel::TWO->config(),
            'levelThree' => UserLevel::THREE->config(),
            'bets' => [
                'info' => __('Count Bets in :month', ['month' => now()->format('M Y')]),
                'stat' => $query->clone()->where('created_at', '>=', now()->startOfMonth())->count() ?? 0,
                'last_month' => $query->clone()->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])->count() ?? 0,
            ],
            'amounts' => [
                'info' => __('Total staked :month', ['month' => now()->format('M Y')]),
                'stat' => $query->clone()->where('created_at', '>=', now()->startOfMonth())->sum('amount') ?? 0,
                'last_month' => $query->clone()->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])->sum('amount') ?? 0,
            ],
            'profitLoss' => [
                'info' => __('P&L since :year', ['year' => now()->subYear()->format('M Y')]),
                'stat' => Stake::query()->where('is_trade_out', true)
                    ->where('created_at', '>=', now()->subYear())
                    ->sum('profit_loss') ?? 0
            ]
        ]);
    }
    /**
     * Show the users promotions page.
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function promotions(Request $request) {}
    /**
     * Show the users feedback page.
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function feedback(Request $request)
    {
        $request->validate([
            'feedback' => ['required', 'string'],
        ]);
        $feedback = new Feedback;
        $feedback->user_id = $request->user()->id;
        $feedback->feedback = $request->feedback;
        $feedback->save();
        return back();
    }

    /**
     * Request another level
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function optin(Request $request)
    {
        if (!$request->level) return back()->with('error', __('Unknown Level'));
        $user = $request->user();
        $lvl = UserLevel::make($request->level);
        $fee = (float) $lvl->settings('optin_fees');
        if ($user->balance < $fee) return back()->with('error', 'Your Balance is Low. Level Optin fee is :fee USD', ['fee' => $fee]);
        $user->requested_next_level = $request->level;
        $user->save();
        return back();
    }

    /**
     * Upload Address verification documents
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function verifyAddress(Request $request)
    {
        $request->validate([
            'type' => ['required', new Enum(PersonalProofOfAddressType::class)],
            'image_uri' => ['required', 'string'],
            'image_upload' => ['required', 'boolean'],
            'image_path' => ['nullable', 'array'],
        ]);
        if ($request->user()->kyc_verified_at) return back()->with('errors', __('Your address is already verified'));
        if (!settings('site.enable_kyc')) return back()->with('errors', __('Kyc is not required currentyly'));
        $personal = $request->user()->personal;
        $upload = app(Uploads::class)->upload($request,  $personal, 'image');
        $personal->proof_of_address =  $upload->url;
        $personal->proof_of_address_type =  $request->type;
        $personal->save();
        return back();
    }

    /**
     * Upload Address verification documents
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function verifyIdentity(Request $request)
    {
        $request->validate([
            'type' => ['required', new Enum(PersonalProofOfIdentityType::class)],
            'image_uri' => ['required', 'string'],
            'image_upload' => ['required', 'boolean'],
            'image_path' => ['nullable', 'array'],
        ]);
        if ($request->user()->kyc_verified_at) return back()->with('errors', __('Your identity is already verified'));
        if (!settings('site.enable_kyc')) return back()->with('errors', __('Kyc is not required currentyly'));
        $personal = $request->user()->personal;
        $upload = app(Uploads::class)->upload($request,  $personal, 'image');
        $personal->proof_of_identity =  $upload->url;
        $personal->proof_of_identity_type =  $request->type;
        $personal->save();
        return back();
    }
}
