<?php

namespace App\Support;

use App\Models\Bet;
use App\Enums\BetMode;
use App\Enums\BothHalfsWinner;
use App\Enums\ExactScores;
use App\Enums\GoalCount;
use App\Enums\Goals;
use App\Enums\HighestScores;
use App\Enums\MixedOutcomes;
use App\Enums\OverUnders;
use App\Enums\Range;
use App\Enums\Result;
use App\Enums\ResultTime;
use App\Models\Coin;
use App\Models\Market;
use Str;

class BetSeeder
{

    public static function seed()
    {

        static::createWinEitherHalfs();
        static::createWinBothHalfs();
        static::createScoresFirstHalf();
        static::createScoresBothHalfs();
        static::createHighestScoringHalf();
        static::createHalfTimeFullTime();
        static::createGoalsRange();
        static::createGoalsOverUnder();
        static::createGameResult();
        static::createExactScores();
        static::createExactGoals();
        static::createEvenOddResult();
        static::createAsianHandicap();
        static::createBothTeamsToScore();
        static::createCleanSheet();
    }

    protected static function getOddId($name)
    {
        $oddsJson = '[{"id":1,"name":"Match Winner"},{"id":2,"name":"Home/Away"},{"id":3,"name":"Second Half Winner"},{"id":4,"name":"Asian Handicap"},{"id":5,"name":"Goals Over/Under"},{"id":6,"name":"Goals Over/Under First Half"},{"id":7,"name":"HT/FT Double"},{"id":8,"name":"Both Teams Score"},{"id":9,"name":"Handicap Result"},{"id":10,"name":"Exact Score"},{"id":11,"name":"Highest Scoring Half"},{"id":12,"name":"Double Chance"},{"id":13,"name":"First Half Winner"},{"id":14,"name":"Team To Score First"},{"id":15,"name":"Team To Score Last"},{"id":16,"name":"Total - Home"},{"id":17,"name":"Total - Away"},{"id":18,"name":"Handicap Result - First Half"},{"id":19,"name":"Asian Handicap First Half"},{"id":20,"name":"Double Chance - First Half"},{"id":21,"name":"Odd/Even"},{"id":22,"name":"Odd/Even - First Half"},{"id":23,"name":"Home Odd/Even"},{"id":24,"name":"Results/Both Teams Score"},{"id":25,"name":"Result/Total Goals"},{"id":26,"name":"Goals Over/Under - Second Half"},{"id":27,"name":"Clean Sheet - Home"},{"id":28,"name":"Clean Sheet - Away"},{"id":29,"name":"Win to Nil - Home"},{"id":30,"name":"Win to Nil - Away"},{"id":31,"name":"Correct Score - First Half"},{"id":32,"name":"Win Both Halves"},{"id":33,"name":"Double Chance - Second Half"},{"id":34,"name":"Both Teams Score - First Half"},{"id":35,"name":"Both Teams To Score - Second Half"},{"id":36,"name":"Win To Nil"},{"id":37,"name":"Home win both halves"},{"id":38,"name":"Exact Goals Number"},{"id":39,"name":"To Win Either Half"},{"id":40,"name":"Home Team Exact Goals Number"},{"id":41,"name":"Away Team Exact Goals Number"},{"id":42,"name":"Second Half Exact Goals Number"},{"id":43,"name":"Home Team Score a Goal"},{"id":44,"name":"Away Team Score a Goal"},{"id":45,"name":"Corners Over Under"},{"id":46,"name":"Exact Goals Number - First Half"},{"id":47,"name":"Winning Margin"},{"id":48,"name":"To Score In Both Halves By Teams"},{"id":49,"name":"Total Goals/Both Teams To Score"},{"id":50,"name":"Goal Line"},{"id":51,"name":"Halftime Result/Total Goals"},{"id":52,"name":"Halftime Result/Both Teams Score"},{"id":53,"name":"Away win both halves"},{"id":54,"name":"First 10 min Winner"},{"id":55,"name":"Corners 1x2"},{"id":56,"name":"Corners Asian Handicap"},{"id":57,"name":"Home Corners Over/Under"},{"id":58,"name":"Away Corners Over/Under"},{"id":59,"name":"Own Goal"},{"id":60,"name":"Away Odd/Even"},{"id":61,"name":"To Qualify"},{"id":62,"name":"Correct Score - Second Half"},{"id":63,"name":"Odd/Even - Second Half"},{"id":72,"name":"Goal Line (1st Half)"},{"id":73,"name":"Both Teams to Score 1st Half - 2nd Half"},{"id":74,"name":"10 Over/Under"},{"id":75,"name":"Last Corner"},{"id":76,"name":"First Corner"},{"id":77,"name":"Total Corners (1st Half)"},{"id":78,"name":"RTG_H1"},{"id":79,"name":"Cards European Handicap"},{"id":80,"name":"Cards Over/Under"},{"id":81,"name":"Cards Asian Handicap"},{"id":82,"name":"Home Team Total Cards"},{"id":83,"name":"Away Team Total Cards"},{"id":84,"name":"Total Corners (3 way) (1st Half)"},{"id":85,"name":"Total Corners (3 way)"},{"id":86,"name":"RCARD"},{"id":87,"name":"Total ShotOnGoal"},{"id":88,"name":"Home Total ShotOnGoal"},{"id":89,"name":"Away Total ShotOnGoal"},{"id":91,"name":"Total Goals (3 way)"},{"id":92,"name":"Anytime Goal Scorer"},{"id":93,"name":"First Goal Scorer"},{"id":94,"name":"Last Goal Scorer"},{"id":95,"name":"To Score Two or More Goals"},{"id":96,"name":"Last Goal Scorer"},{"id":97,"name":"First Goal Method"},{"id":99,"name":"To Score A Penalty"},{"id":100,"name":"To Miss A Penalty"},{"id":101,"name":null},{"id":102,"name":"Player to be booked"},{"id":103,"name":"Player to be Sent Off"},{"id":104,"name":"Asian Handicap (2nd Half)"},{"id":105,"name":"Home Team Total Goals(1st Half)"},{"id":106,"name":"Away Team Total Goals(1st Half)"},{"id":107,"name":"Home Team Total Goals(2nd Half)"},{"id":108,"name":"Away Team Total Goals(2nd Half)"},{"id":109,"name":"Draw No Bet (1st Half)"},{"id":110,"name":"Scoring Draw"},{"id":111,"name":"Home team will score in both halves"},{"id":112,"name":"Away team will score in both halves"},{"id":113,"name":"Both Teams To Score in Both Halves"},{"id":114,"name":"Home Team Score a Goal (1st Half)"},{"id":115,"name":"Home Team Score a Goal (2nd Half)"},{"id":116,"name":"Away Team Score a Goal (1st Half)"},{"id":117,"name":"Away Team Score a Goal (2nd Half)"},{"id":118,"name":"Home Win/Over"},{"id":119,"name":"Home Win/Under"},{"id":120,"name":"Away Win/Over"},{"id":121,"name":"Away Win/Under"},{"id":122,"name":"Home team will win either half"},{"id":123,"name":"Away team will win either half"},{"id":124,"name":"Home Come From Behind and Win"},{"id":125,"name":"Corners Asian Handicap (1st Half)"},{"id":126,"name":"Corners Asian Handicap (2nd Half)"},{"id":127,"name":"Total Corners (2nd Half)"},{"id":128,"name":"Total Corners (3 way) (2nd Half)"},{"id":129,"name":"Away Come From Behind and Win"},{"id":130,"name":"Corners 1x2 (1st Half)"},{"id":131,"name":"Corners 1x2 (2nd Half)"},{"id":132,"name":"Home Total Corners (1st Half)"},{"id":133,"name":"Home Total Corners (2nd Half)"},{"id":134,"name":"Away Total Corners (1st Half)"},{"id":135,"name":"Away Total Corners (2nd Half)"},{"id":136,"name":"1x2 - 15 minutes"},{"id":137,"name":"1x2 - 60 minutes"},{"id":138,"name":"1x2 - 75 minutes"},{"id":139,"name":"1x2 - 30 minutes"},{"id":140,"name":"DC - 30 minutes"},{"id":141,"name":"DC - 15 minutes"},{"id":142,"name":"DC - 60 minutes"},{"id":143,"name":"DC - 75 minutes"},{"id":144,"name":"Goal in 1-15 minutes"},{"id":145,"name":"Goal in 16-30 minutes"},{"id":146,"name":"Goal in 31-45 minutes"},{"id":147,"name":"Goal in 46-60 minutes"},{"id":148,"name":"Goal in 61-75 minutes"},{"id":149,"name":"Goal in 76-90 minutes"},{"id":150,"name":"Home Team Yellow Cards"},{"id":151,"name":"Away Team Yellow Cards"},{"id":152,"name":"Yellow Asian Handicap"},{"id":153,"name":"Yellow Over/Under"},{"id":154,"name":"Yellow Double Chance"},{"id":155,"name":"Yellow Over/Under (1st Half)"},{"id":156,"name":"Yellow Over/Under (2nd Half)"},{"id":157,"name":"Yellow Odd/Even"},{"id":158,"name":"Yellow Cards 1x2"},{"id":159,"name":"Yellow Asian Handicap (1st Half)"},{"id":160,"name":"Yellow Asian Handicap (2nd Half)"},{"id":161,"name":"Yellow Cards 1x2 (1st Half)"},{"id":162,"name":"Yellow Cards 1x2 (2nd Half)"},{"id":163,"name":"Penalty Awarded"},{"id":164,"name":"Offsides Total"},{"id":165,"name":"Offsides 1x2"},{"id":166,"name":"Offsides Handicap"},{"id":167,"name":"Offsides Home Total"},{"id":168,"name":"Offsides Away Total"},{"id":169,"name":"Offsides Double Chance"},{"id":170,"name":"Fouls. Away Total"},{"id":171,"name":"Fouls. Home Total"},{"id":172,"name":"Fouls. Double Chance"},{"id":173,"name":"Fouls. Total"},{"id":174,"name":"Fouls. Handicap"},{"id":175,"name":"Fouls. 1x2"},{"id":176,"name":"ShotOnTarget 1x2"},{"id":177,"name":"ShotOnTarget Handicap"},{"id":178,"name":"ShotOnTarget Double Chance"},{"id":179,"name":"First Team to Score"},{"id":180,"name":"Last Team to Score"},{"id":181,"name":"European Handicap (2nd Half)"},{"id":182,"name":"Draw No Bet (2nd Half)"},{"id":183,"name":"Double Chance/Total"},{"id":184,"name":"To Score in Both Halves"},{"id":185,"name":"First Team to Score (3 way) 1st Half"},{"id":186,"name":"Total Goals Number By Ranges"},{"id":187,"name":"Total Goals By Ranges (1st Half)"},{"id":188,"name":"Clean Sheet"},{"id":189,"name":"To Advance Handicap"},{"id":190,"name":"Home Exact Goals Number (1st Half)"},{"id":191,"name":"Away Exact Goals Number (1st Half)"},{"id":192,"name":"Home Highest Scoring Half"},{"id":193,"name":"Away Highest Scoring Half"},{"id":194,"name":"Result/Total Goals (2nd Half)"},{"id":195,"name":"Either Team Wins By 1 Goals"},{"id":196,"name":"Either Team Wins By 2 Goals"},{"id":197,"name":"Over/Under 15m-30m"},{"id":198,"name":"Over/Under 30m-45m"},{"id":199,"name":"Home Win To Nill (1st Half)"},{"id":200,"name":"Home Win To Nill (2nd Half)"},{"id":201,"name":"To Score In 1st Half"},{"id":202,"name":"To Score In 2nd Half"},{"id":203,"name":"Yellow Cards. Odd/Even (1st Half)"},{"id":204,"name":"Yellow Cards. Odd/Even (2nd Half)"},{"id":205,"name":"First Team to Score (3 way) 2nd Half"},{"id":206,"name":"Home No Bet"},{"id":207,"name":"Away No Bet"},{"id":208,"name":"Corners. First Corner (3 way)"},{"id":209,"name":"Home Come From Behind and Draw"},{"id":210,"name":"Away Come From Behind and Draw"},{"id":211,"name":"Total Shots"},{"id":212,"name":"Player Assists"},{"id":213,"name":"Player Triples"},{"id":214,"name":"Player Points"},{"id":215,"name":"Player Singles"},{"id":216,"name":"Multi Touchdown Scorer (2 or More)"},{"id":217,"name":"Multi Touchdown Scorer (3 or More)"},{"id":218,"name":"Away Anytime Goal Scorer"},{"id":219,"name":"Away First Goal Scorer"},{"id":220,"name":"Shots. Away Total"},{"id":221,"name":"Shots. Home Total"},{"id":222,"name":"To Win From Behind"},{"id":223,"name":"Number of Goals In Match (Range)"},{"id":224,"name":"Game Decided After Penalties"},{"id":225,"name":"Game Decided in Extra Time"},{"id":226,"name":"Away Last Goal Scorer"},{"id":227,"name":"Goal Method Header"},{"id":228,"name":"Home Goal Method Header"},{"id":229,"name":"Goal Method Outside the Box"},{"id":230,"name":"Home Goal Method Outside the Box"},{"id":231,"name":"Home Anytime Goal Scorer"},{"id":232,"name":"Home First Goal Scorer"},{"id":233,"name":"Home Last Goal Scorer"},{"id":234,"name":"Home To Score Three or More Goals"},{"id":235,"name":"Away To Score Three or More Goals"},{"id":236,"name":"Away To Score Two or More Goals"},{"id":237,"name":"Home To Score Two or More Goals"},{"id":238,"name":"Home Team Goalscorers First"},{"id":239,"name":"Corners. European Handicap"},{"id":240,"name":"Home Player Shots"},{"id":241,"name":"Away Player Shots"},{"id":242,"name":"Player Shots On Target"},{"id":243,"name":"Home Shots On Target"},{"id":244,"name":"Away Shots On Target"},{"id":245,"name":"Away Goal Method Header"},{"id":246,"name":"Away Goal Method Outside the Box"},{"id":247,"name":"Corners Race To"},{"id":248,"name":"Time Of 1st Score"},{"id":249,"name":"Multicorners"},{"id":250,"name":"First Card Received (3 way)"},{"id":251,"name":"Player to be booked"},{"id":252,"name":"Both Teams to Receive a Card"},{"id":253,"name":"Time Of 1st Score"},{"id":254,"name":"Team Performances (Range)"},{"id":255,"name":"Home Player Assists"},{"id":256,"name":"Away Player Assists"},{"id":257,"name":"Player to Score or Assist"},{"id":258,"name":"Home Player to Score or Assist"},{"id":259,"name":"Away Player to Score or Assist"},{"id":260,"name":"Team Time Of 1st Score"},{"id":261,"name":"Total Goal Minutes (Range)"},{"id":262,"name":"Late Goal (Range)"},{"id":263,"name":"Early Goal (Range)"},{"id":264,"name":"Player Shots On Target Total"},{"id":265,"name":"Player Shots Total"},{"id":266,"name":"Player Fouls Committed"},{"id":267,"name":"Goalkeeper Saves"},{"id":268,"name":"Home Goalkeeper Saves"},{"id":269,"name":"Home Player Shots On Target Total"},{"id":270,"name":"Home Player Shots Total"},{"id":271,"name":"Home Player Fouls Committed"},{"id":272,"name":"Home Player Tackles"},{"id":273,"name":"Home Player Passes"},{"id":274,"name":"Away Goalkeeper Saves"},{"id":275,"name":"Away Player Shots On Target Total"},{"id":276,"name":"Away Player Shots Total"},{"id":277,"name":"Away Player Fouls Committed"},{"id":278,"name":"Away Player Tackles"},{"id":279,"name":"Away Player Passes"},{"id":280,"name":"First Set Piece 5 Minutes"},{"id":281,"name":"Total Tackles"},{"id":282,"name":"Double Chance/Both Teams To Score"},{"id":283,"name":"Away Win To Nill (1st Half)"},{"id":284,"name":"Away Win To Nill (2nd Half)"},{"id":285,"name":"Team To Score (Goals)"},{"id":286,"name":"Team Goalscorers First"},{"id":287,"name":"Team Goalscorers Last"},{"id":288,"name":"Home Team Goalscorers Last"},{"id":289,"name":"Away Team Goalscorers First"},{"id":290,"name":"Away Team Goalscorers Last"},{"id":291,"name":"Time of First Goal Brackets (Range)"},{"id":292,"name":"Over/Under between 0 and 10m"},{"id":293,"name":"Double Chance 0-15m"},{"id":294,"name":"Double Chance 15-30m"},{"id":295,"name":"Corners. Total (Range)"},{"id":296,"name":"Double Chance 30-45m"},{"id":297,"name":"Corners. total between 0 and 10m"},{"id":298,"name":"Method of Victory"},{"id":299,"name":"Cards over/under between 0 and 10 m"},{"id":300,"name":"Both Teams to Receive 2+ Cards"},{"id":301,"name":"Tackles. Away Total"},{"id":302,"name":"Tackles. Home Total"},{"id":303,"name":"Over/Under between 0 and 10 m"},{"id":304,"name":"Over/Under between  0 and 10 m"}]';
        $odds = json_decode($oddsJson);
        foreach ($odds as $odd) {
            if ($odd->name == $name) {
                return $odd->id;
            }
        }
    }



    protected static function createWinEitherHalfs()
    {
        $name = "WinnerOfEitherHalfs";
        $market = Market::query()
            ->updateOrCreate([
                'slug' => Str::of("{$name}")->snake()->slug()
            ], [
                'mode' => BetMode::WinEitherHalfs,
                'name' => $name,
            ]);
        foreach (BothHalfsWinner::cases() as $bett) {
            $bet = Bet::query()
                ->updateOrCreate([
                    'market_id' => $market->id,
                    'name' =>   Str::of($market->slug . '-' . $bett->name)->snake()->slug(),
                ], [
                    'result' => $bett->value,
                    'mode' => BetMode::WinEitherHalfs
                ]);
            $bet->generateBetId()->save();
        }
        $market->generateMarketId()->save();
    }




    /**
     * bet on who will win both halfs of a game
     */
    protected static function createWinBothHalfs()
    {
        $name = "WinnerOfBothHalfs";
        $market = Market::query()
            ->updateOrCreate([
                'slug' => Str::of("{$name}")->snake()->slug()
            ], [
                'mode' => BetMode::WinBothHalfs,
                'name' => $name,
            ]);
        $list = BothHalfsWinner::cases();
        foreach ($list as $bett) {
            if ($bett == BothHalfsWinner::both) continue;
            $bet = Bet::query()
                ->updateOrCreate([
                    'market_id' => $market->id,
                    'name' =>   Str::of("{$market->slug}-{$bett->name}")->snake()->slug(),
                ], [
                    'result' => $bett->value,
                    'mode' => BetMode::WinBothHalfs,
                ]);
            $bet->generateBetId()->save();
        }
        $market->generateMarketId()->save();
    }



    protected static function createScoresFirstHalf()
    {
        $name = "ScoresFirstHalf";
        $market = Market::query()
            ->updateOrCreate([
                'slug' => Str::of("{$name}")->snake()->slug()
            ], [
                'mode' => BetMode::ScoresFirstHalf,
                'name' => $name,
            ]);
        foreach (BothHalfsWinner::cases() as $bett) {
            $bet = Bet::query()
                ->updateOrCreate([
                    'market_id' => $market->id,
                    'name' =>   Str::of("{$market->slug}-{$bett->name}")->snake()->slug(),
                ], [
                    'result' => $bett->value,
                    'mode' => BetMode::ScoresFirstHalf
                ]);
            $bet->generateBetId()->save();
        }
        $market->generateMarketId()->save();
    }



    protected static function createScoresBothHalfs()
    {
        $name = "ScoresBothHalfs";
        $market = Market::query()
            ->updateOrCreate([
                'slug' => Str::of("{$name}")->snake()->slug()
            ], [
                'mode' => BetMode::ScoreBothHalfs,
                'name' => $name,
            ]);
        foreach (BothHalfsWinner::cases() as $bett) {
            $bet = Bet::query()
                ->updateOrCreate([
                    'market_id' => $market->id,
                    'name' =>   Str::of("{$market->slug}-{$bett->name}")->snake()->slug(),
                ], [
                    'result' => $bett->value,
                    'mode' => BetMode::ScoreBothHalfs
                ]);
            $bet->generateBetId()->save();
        }
        $market->generateMarketId()->save();
    }




    /**
     * Place a bet on Highest Scoring Half of a game;
     */
    protected static function createHighestScoringHalf()
    {
        $highestScoreOutcomes = HighestScores::cases();
        $list = GoalCount::cases();
        foreach ($list as $team) {
            $name = "HighestScoringHalf";
            $market = Market::query()
                ->updateOrCreate([
                    'slug' => Str::of("{$team->name}-{$name}")->snake()->slug()
                ], [
                    'mode' => BetMode::HighestScoringHalf,
                    'name' => $name,
                ]);
            foreach ($highestScoreOutcomes as $highestScoreOutcome) {
                $bet = Bet::query()
                    ->updateOrCreate(
                        [
                            'market_id' => $market->id,
                            'name' =>   Str::of("{$market->slug}-{$highestScoreOutcome->name}")->snake()->slug(),
                        ],
                        [
                            'result' => $highestScoreOutcome->value,
                            'mode' => BetMode::HighestScoringHalf,
                            'team' => $team,
                        ]
                    );
                $bet->generateBetId()->save();
            }
            $market->generateMarketId()->save();
        }
    }




    /**
     * Place a bet on Exact on the result of a game;
     * @param mixedOutcome MixedOutcomes result expected for the game
     */
    protected static function createHalfTimeFullTime()
    {
        $name = "HalfTimeFullTime";
        $market = Market::query()
            ->updateOrCreate([
                'slug' => Str::of("{$name}")->snake()->slug()
            ], [
                'mode' => BetMode::HalfTimeFullTime,
                'name' => $name,
            ]);
        foreach (MixedOutcomes::cases() as $mixedOutcome) {
            $bet = Bet::query()
                ->updateOrCreate([
                    'market_id' => $market->id,
                    'name' =>   Str::of("{$market->slug}-{$mixedOutcome->name}")->snake()->slug(),
                ], [
                    'result' => $mixedOutcome->value,
                    'mode' => BetMode::HalfTimeFullTime
                ]);
            $bet->generateBetId()->save();
        }
        $market->generateMarketId()->save();
    }




    /**
     * Place a bet on Exact on the result of a game;
     * @param rangeOutcome Results range expected for the match
     * @param gameTime the time at which results are evaluated
     */

    protected static function createGoalsRange()
    {
        $rangeOutcomes = Range::cases();
        $list = ResultTime::cases();
        foreach ($list as $gameTime) {
            $name = "GoalsRange";
            $market = Market::query()
                ->updateOrCreate([
                    'slug' => Str::of("{$gameTime->name}-{$name}")->snake()->slug()
                ], [
                    'mode' => BetMode::GoalsRange,
                    'name' => $name,
                ]);
            foreach ($rangeOutcomes as $rangeOutcome) {
                $bet = Bet::query()
                    ->updateOrCreate([
                        'market_id' => $market->id,
                        'name' =>   Str::of("{$market->slug}-{$rangeOutcome->name}")->snake()->slug(),
                    ], [
                        'result' => $rangeOutcome->value,
                        'mode' => BetMode::GoalsRange,
                        'gameTime' => $gameTime,
                    ]);
                $bet->generateBetId()->save();
            }
            $market->generateMarketId()->save();
        }
    }


    /**
     * Goals OverUnders line per pool;
     * Place a bet on Exact on the result of a game;
     * @param outcome Results expected for the match (home draw away)
     * @param gameTime the time at which results are evaluated
     */

    protected static function createGoalsOverUnder()
    {
        $boolOutcomes = [true, false];
        $lines = OverUnders::cases();
        $list = ResultTime::cases();
        $teamScores = GoalCount::cases();
        foreach ($lines as $line) {
            foreach ($teamScores as $teamScore) {
                foreach ($list as $gameTime) {
                    $name = "OverUnder";
                    $market = Market::query()
                        ->updateOrCreate([
                            'slug' => Str::of("{$teamScore->name}-{$gameTime->name}-{$name}-{$line->name}")->snake()->slug()
                        ], [
                            'mode' => BetMode::GoalsOverUnder,
                            'name' => $name,
                        ]);
                    foreach ($boolOutcomes as $boolOutcome) {
                        $bet = Bet::query()
                            ->updateOrCreate([
                                'market_id' => $market->id,
                                'name' =>   Str::of("{$market->slug}-" . ($boolOutcome ? 'true' : 'false'))->snake()->slug(),
                            ], [
                                'boolOutcome' => $boolOutcome,
                                'gameTime' => $gameTime,
                                'team' => $teamScore,
                                'result' => $line->value,
                                'mode' => BetMode::GoalsOverUnder,
                            ]);
                        $bet->generateBetId()->save();
                        $bets[] = $bet;
                    }
                    $market->generateMarketId()->save();
                }
            }
        }
    }



    /**
     * Place a bet on Exact on the result of a game;
     * @param outcome Results expected for the match (home draw away)
     * @param gameTime the time at which results are evaluated
     */

    protected static function createGameResult()
    {
        $resultOutcomes = Result::cases();
        $list = ResultTime::cases();
        foreach ($list as $gameTime) {
            $name = "GameResult";
            $market = Market::query()
                ->updateOrCreate([
                    'slug' => Str::of("{$gameTime->name}-{$name}")->snake()->slug()
                ], [
                    'mode' => BetMode::GameResult,
                    'name' => $name,
                ]);
            foreach ($resultOutcomes as $resultOutcome) {
                if ($resultOutcome == Result::PENDING || $resultOutcome == Result::CANCELLED)
                    continue;
                $bet = Bet::query()
                    ->updateOrCreate([
                        'market_id' => $market->id,
                        'name' =>   Str::of("{$market->slug}-{$resultOutcome->name}")->snake()->slug(),
                    ], [
                        'result' => $resultOutcome->value,
                        'gameTime' => $gameTime,
                        'mode' => BetMode::GameResult,
                    ]);
                $bet->generateBetId()->save();
            }
            $market->generateMarketId()->save();
        }
    }



    /**
     * Place a bet on Exact scores for a game;
     * @param exactScoreOutcome Exact score expected
     * @param gameTime the time at which results are evaluated
     */
    protected static function createExactScores()
    {
        $exactScoreOutcomes = ExactScores::cases();
        $list = ResultTime::cases();
        foreach ($list as $gameTime) {
            $name = "ExactScores";
            $market = Market::query()
                ->updateOrCreate([
                    'slug' => Str::of("{$gameTime->name}-{$name}")->snake()->slug()
                ], [
                    'mode' => BetMode::ExactScore,
                    'name' => $name,
                ]);
            foreach ($exactScoreOutcomes as $exactScoreOutcome) {
                $bet = Bet::query()
                    ->updateOrCreate([
                        'market_id' => $market->id,
                        'name' =>   Str::of("{$market->slug}-{$exactScoreOutcome->name}")->snake()->slug(),
                    ], [
                        'result' => $exactScoreOutcome->value,
                        'gameTime' => $gameTime,
                        'mode' => BetMode::ExactScore,
                    ]);
                $bet->generateBetId()->save();
            }
            $market->generateMarketId()->save();
        }
    }



    /**
     * Place a bet on Exact goals to be scored;
     * @param outcome Goals expected
     * @param gameTime the time at which results are evaluated
     * @param teamScore which team scores to consider
     */
    protected static function createExactGoals()
    {
        $outcomes = Goals::cases();
        $teamScores = GoalCount::cases();
        $list = ResultTime::cases();
        foreach ($teamScores as $teamScore) {
            foreach ($list as $gameTime) {
                $name = "ExactGoals";
                $market = Market::query()
                    ->updateOrCreate([
                        'slug' => Str::of("{ $teamScore->name }-{$gameTime->name}-{$name}")->snake()->slug()
                    ], [
                        'mode' => BetMode::ExactGoals,
                        'name' => $name,
                    ]);
                foreach ($outcomes as $outcome) {
                    $bet = Bet::query()
                        ->updateOrCreate([
                            'market_id' => $market->id,
                            'name' =>   Str::of("{$market->slug}-{$outcome->name}")->snake()->slug(),
                        ], [
                            'result' => $outcome->value,
                            'team' => $teamScore,
                            'gameTime' => $gameTime,
                            'mode' => BetMode::ExactGoals
                        ]);
                    $bet->generateBetId()->save();
                }
                $market->generateMarketId()->save();
            }
        }
    }


    /**
     * Place a bet on EvenOddResult;
     * @param isEven if result will be even
     * @param gameTime the time at which results are evaluated
     * @param teamScore which team scores to consider
     */

    protected static function createEvenOddResult()
    {
        $outcomes = [true, false];
        $teamScores = GoalCount::cases();
        $list = ResultTime::cases();
        foreach ($teamScores as $teamScore) {
            foreach ($list as $gameTime) {
                $name = "EvenOddResult";
                $market = Market::query()
                    ->updateOrCreate([
                        'slug' => Str::of("{ $teamScore->name }-{$gameTime->name}-{$name}")->snake()->slug()
                    ], [
                        'mode' => BetMode::EvenOddResult,
                        'name' => $name,
                    ]);
                foreach ($outcomes as $outcome) {
                    $bet = Bet::query()
                        ->updateOrCreate([
                            'market_id' => $market->id,
                            'name' =>   Str::of("{$market->slug}-" . ($outcome ? 'true' : 'false'))->snake()->slug(),
                        ], [
                            'boolOutcome' => $outcome,
                            'team' => $teamScore,
                            'gameTime' => $gameTime,
                            'mode' => BetMode::EvenOddResult
                        ]);
                    $bet->generateBetId()->save();
                }
                $market->generateMarketId()->save();
            }
        }
    }

    /**
     * Place a bet on Asian handicap 0.5 - ;
     * @param asianHandicapOutcome the expected double chance
     * @param gameTime the time at which results are evaluated
     */
    protected static function createAsianHandicap()
    {
        $lines = OverUnders::cases();
        $teamScores = GoalCount::cases();
        $list = ResultTime::cases();
        foreach ($list as $gameTime) {
            if ($gameTime == ResultTime::secondHalf) continue;
            foreach ($lines as $line) {
                if ($line->value > 3) continue; // end on 3.5//
                foreach ($teamScores as $teamScore) {
                    if ($teamScore == GoalCount::total) continue; // no total
                    $name = "AsianHandicap";
                    $market = Market::query()
                        ->updateOrCreate([
                            'slug' => Str::of("{ $teamScore->name }-{$gameTime->name}-{$line->name}-{$name}")
                                ->snake()->slug()
                        ], [
                            'mode' => BetMode::AsianHandiCap,
                            'name' => $name,
                        ]);
                    foreach ([true, false] as $bool) {
                        // use updateOrCreate to avoid duplicates
                        $bet = Bet::query()
                            ->updateOrCreate([
                                'market_id' => $market->id,
                                'name' =>   Str::of("{$market->slug}-" . ($bool ? 'True' : 'false'))->snake()->slug(),
                            ], [
                                'result' => $line->value,
                                'team' => $teamScore,
                                'gameTime' => $gameTime,
                                'boolOutcome' => $bool,
                                'mode' => BetMode::AsianHandiCap
                            ]);
                        $bet->generateBetId()->save();
                    }
                    $market->generateMarketId()->save();
                }
            }
        }
    }



    /**
     * Place a bet on Both Teams To Score;
     * @param outcome true or false
     * @param gameTime the time at which results are evaluated
     */
    protected static function createBothTeamsToScore()
    {
        $gameTimes = ResultTime::cases();
        $outcomes = [true, false];
        foreach ($gameTimes as $gameTime) {
            $name = "BothTeamsToScore";
            $market = Market::query()
                ->updateOrCreate([
                    'slug' => Str::of("{$gameTime->name}-{$name}")->snake()->slug()
                ], [
                    'mode' => BetMode::BothTeamsToScore,
                    'name' => $name,
                ]);
            foreach ($outcomes as $outcome) {
                // use updateOrCreate to avoid duplicates
                $bet = Bet::query()
                    ->updateOrCreate([
                        'market_id' => $market->id,
                        'name' =>   Str::of("{$market->slug}-" . ($outcome ? 'true' : 'false'))->snake()->slug(),
                    ], [
                        'boolOutcome' => $outcome,
                        'gameTime' => $gameTime,
                        'mode' => BetMode::BothTeamsToScore
                    ]);

                $bet->save();
                $bet->generateBetId()->save();
            }
            $market->generateMarketId()->save();
        }
    }




    /**
     * Place a bet on Clean sheet outcome;
     * @param outcome true or false
     * @param gameTime the time at which results are evaluated
     */
    protected static function createCleanSheet()
    {
        $teams = BothHalfsWinner::cases();
        $gameTimes = ResultTime::cases();
        foreach ($gameTimes as $gameTime) {
            $name = "CleanSheet";
            $market = Market::query()
                ->updateOrCreate([
                    'slug' => Str::of("{$gameTime->name}-{$name}")->snake()->slug()
                ], [
                    'mode' => BetMode::CleanSheet,
                    'name' => $name,
                ]);
            foreach ($teams as $team) {
                $bet = Bet::query()
                    ->updateOrCreate([
                        'market_id' => $market->id,
                        'name' =>   Str::of("{$market->slug}-{$team->name}")->snake()->slug(),
                    ], [
                        'result' => $team->value,
                        'gameTime' => $gameTime,
                        'mode' => BetMode::CleanSheet
                    ]);
                $bet->generateBetId()->save();
            }
            $market->generateMarketId()->save();
        }
    }

    /**
     * seed an compondbet based on two bets;
     * @param Market $market1
     * @param Market $market2
     * @param Coin $coin
     */
    protected static function compoundMarkets(Market $market1, Market $market2)
    {
        $hashes1 = $market1->bets()->pluck('id')->all();
        $hashes2 = $market2->bets()->pluck('id')->all();
        $groups = $hashes1->crossJoin($hashes2->all());
        $name = "compoundMarket";
        $market = Market::query()
            ->updateOrCreate([
                'slug' => $market1->slug . '+' . $market2->slug,
            ], [
                'name' => $name,
                'mode' => BetMode::CompoundBet,
                'compoundHashes' => [
                    $market1->marketId,
                    $market2->marketId,
                ],
                'isCompound' => true,
            ]);
        $market->compounds()->sync([$market1->id, $market2->id]);
        foreach ($groups as $group) {
            $bets = Bet::find($group);
            $bet = Bet::query()
                ->updateOrCreate([
                    'market_id' => $market->id,
                    'name' =>   Str::of($bets->map(fn ($b) => $b->name)->implode("-"))->snake()->slug(),
                ], [
                    'compoundBet' =>  $bets->pluck('betId')->all(),
                    'mode' => BetMode::CompoundBet,
                ]);
            $bet->generateBetId()->save();
        }
        $market->generateMarketId()->save();
    }
}
