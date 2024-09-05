<?php

namespace Database\Seeders;

use App\Enums\CurrencyDisplay;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tos = require(__DIR__ . '/data/terms.php');
        //
        $config = [
            'level_one' => [
                'name' => 'Standard',
                'description' => 'You pay :commissionProfit % commission on net profit per market only',
                'limits' => 'Max :exchangeMaxBet bets placed per calendar month',
                'max_daily_deposit' => 1000,
                'max_monthly_deposit' => 5000,
                'exchange_max_bet' => 1000,
                'exchange_min_bet' => 1,
                'bookie_max_bet' => 100,
                'bookie_min_bet' => 1,
                'commission_profit' => 1,
                'commission_bet' => 0,
                'deposit_fees' => 1,
                'withdraw_fees' => 1,
                'optin_fees' => 0,
            ],
            'level_two' => [
                'name' => 'Pro',
                'description' => 'You pay :commissionBet % commission on winnings or losses per each individual bet that settles.',
                'limits' => 'Max :exchangeMaxBet bets placed per calendar month',
                'max_daily_deposit' => 10000,
                'max_monthly_deposit' => 50000,
                'exchange_max_bet' => 10000,
                'exchange_min_bet' => 10,
                'bookie_max_bet' => 10000,
                'bookie_min_bet' => 10,
                'commission_profit' => 0,
                'commission_bet' => 1,
                'deposit_fees' => 0,
                'withdraw_fees' => 0,
                'optin_fees' => 500,
            ],
            'level_three' => [
                'name' => 'Market Maker',
                'description' => 'You pay :commissionProfit % commission on winnings or losses per each individual bet that settles.',
                'limits' => 'Act as an MM, tight spreads, many markets, and good maker ratio.',
                'max_daily_deposit' => 100000,
                'max_monthly_deposit' => 500000,
                'exchange_max_bet' => 100000,
                'exchange_min_bet' => 500,
                'bookie_max_bet' => 0,
                'bookie_min_bet' => 0,
                'commission_profit' => 0.5,
                'commission_bet' => 0,
                'deposit_fees' => 0,
                'withdraw_fees' => 0,
                'optin_fees' => 1000,
            ],
            'pages' => [
                'privacy' => $tos['privacy'],
                'terms' => $tos['terms'],
                'gdpr_notice' => "Cookie Policy",
                'gdpr_terms' => "We and selected third parties use cookies or similar technologies for technical purposes and, with your consent, for other purposes as specified in the cookie policy. We also use cookies to improve your experience on our site. By continuing to use our site, you agree to our use of cookies."
            ],
            'google' => [
                'client_id' => null,
                'client_secret' => null,
                'project_id' => null,
                'enable' => 'true',
            ],
            'github' => [
                'client_id' => null,
                'client_secret' => null,
                'redirect' => null,
                'enable' => 'true',
            ],
            'facebook' => [
                'client_id' => null,
                'client_secret' => null,
                'redirect' => null,
                'enable' => 'true',
            ],
            'twofa' => [
                'enable_2fa' => 'true',
                'confirm' => 'true',
                'window' => null,
            ],
            'site' => [
                'app_name' => 'Betriver',
                'uploads_disk' => 'public',
                'profile_photo_disk' => 'public',
                'description' => 'Advanced Crypto Betting for the next generation',
                'logo' => null,
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'currency_display' => CurrencyDisplay::AUTO->value,
                'timezone' => 'UTC',
                'enable_exchange' => 'true',
                'enable_bookie' => 'true',
                'exchange_max_bet' => 1000,
                'odds_spread' => 0.2,
                'exchange_min_bet' => 1,
                'bookie_max_bet' => 500,
                'bookie_min_bet' => 1,
                'enable_kyc' => 'true',
                'sms_gateway' => null,
                'apifootball_api_key' => null,
                'load_games_for_days' => 14,
                'load_games_start' => 0,
                'update_leagues_cron' =>  'daily',
                'load_games_cron' => 'daily',
                'load_odds_cron' => 'daily',
                'load_results_cron' => 'every_five_minutes',
                'auto_settle_exchange' => 'true',
                'auto_settle_bookie' => 'true',
                'bookie_buy_tickets' => 'true',
                'coincap_apikey' => '55014acc-82b7-4970-9ca1-c042809054da',
            ],
            // sitemap
            'cron' => [
                'update_leagues' =>  'daily',
                'update_games' =>  'daily',
                'update_games_' =>  'daily',
                'games' =>  100,
                'enable_sitemap' => 'true',
                'enable_meta' => 'true',
            ],
            // sitemap
            'sitemap' => [
                'leagues' =>  20,
                'games' =>  100,
                'enable_sitemap' => 'true',
                'enable_meta' => 'true',
            ],
            // page meta
            'home' => [
                'title' => null,
                'keywords' => null,
                'description' => null,
            ],
            'games' => [
                'title' => null,
                'keywords' => null,
                'description' => null,
            ],
            'game' => [
                'title' => null,
                'keywords' => null,
                'description' => null,
            ],
            // commission
            'commission' => [
                'deposit' =>  'true',
                'slip' =>  'true',
                'ticket' =>  'true',
                'cancellation' =>  'true',
            ],
            // sms notification
            'smspoh' => [
                'endpoint' => 'https://smspoh.com/api/v2/send',
                'token' => null,
                'sender' => null
            ],

            'touchsms' => [
                'token_id' => null,
                'access_token' => null,
                'default_sender' => null,
            ],

            'clickatell' => ['apiKey' => null],


            'messagebird' => [
                'access_key' => null,
                'originator' => null,
            ],

            'vonage' => [
                'api_key' => null,
                'api_secret' => null,
                'sms_sender' => null,
            ],

            'textmagic' => [
                'apiv2_key' => null,
                'username' => null,
            ],

            //notification messages
            'mail' => [
                'balance_deposit_message' =>  ':amount :symbol has been credited to your account. Your new balance is :balance :symbol',
                'balance_deposit_subject' =>  '[:appname] :symbol Deposit Confirmed ',
                'balance_deposit_salutation' => ':symbol Deposit Successful',
                'balance_withdraw_message' =>  ':amount :symbol has been credited to your account. Your new balance is :balance :symbol',
                'balance_withdraw_subject' =>  '[:appname] :symbol Withdraw Confirmed ',
                'balance_withdraw_salutation' =>  ':symbol withdraw Successful',
                'bet_lost_message' =>  'Your Bet (:uid) of :amount :symbol did not win. Full bet amount was lost',
                'bet_lost_subject' =>  'Bet [#:uid] did not win',
                'bet_lost_salutation' =>  'Bet settled on a loss',
                'bet_refunded_message' =>  'Your Bet (:uid) was refunded in full and credited to your account.',
                'bet_refunded_subject' => 'REFUND for Bet [#:uid]',
                'bet_refunded_salutation' =>  'Bet was refunded in full',
                'bet_won_message' => 'Your Bet (:uid) of :amount :symbol emerged winner. Your winnings have been credited to your account',
                'bet_won_subject' => 'Bet [#:uid] is a winner',
                'bet_won_salutation' =>  'Congs, You  won!',
                'kyc_completed_message' => 'Congratulations, your KYC verification is complete and any account limits have been removed.',
                'kyc_completed_subject' => 'KYC Verification Complete',
                'kyc_completed_salutation' =>  'You are now verified',
                'kyc_denied_message' => 'To ensure you have continued access to deposits, withdrawals, and bets, please resubmit the KYC verification process as soon as possible.',
                'kyc_denied_subject' => 'KYC Verification Failed',
                'kyc_denied_salutation' =>  'We could not verify your Credentials',
            ],
            'sms' => [
                'balance_deposit_message' =>  ':amount :symbol has been credited to your account.',
                'balance_withdraw_message' =>  ':amount :symbol has been withdrawn from your account.',
                'bet_lost_message' =>  'Your Bet (:uid) of :amount :symbol did not win.',
                'bet_refunded_message' =>  'Your Bet (:uid) was refunded in full.',
                'bet_won_message' => 'Your Bet (:uid) of :amount :symbol emerged winner.',
                'kyc_completed_message' => 'Congratulations, your KYC verification is complete.',
                'kyc_denied_message' => 'Kyc submission rejected, resubmit as soon as possible.',
            ],
            'notifications' => [
                'enable_sms' => 'true',
                'enable_email' => 'true',
                'mailer' => 'smtp',
                'sms' => 'vonage',
                'from_address' => 'admin@coderiver.io',
                'from_name' => 'Coderiver Support',
            ],
            'smtp' => [
                'host' => 'mailpit',
                'port' => 1025,
                'username' => null,
                'password' => null,
                'encryption' => null,
            ],
            'mailgun' => [
                'secret' => null,
                'domain' => null,
            ],
            'postmark' => [
                'token' => null,
            ],
            'mailjet' => [
                'key' => null,
                'secret' => null,
            ],
            'sendgrid' => [
                'key' => null,
            ],
        ];
        foreach ($config as $group => $settings) {
            $rows = collect($settings)->map(fn($val, $name) => [
                'name' => $name,
                'val' => $val,
                'group' => $group,
            ]);
            Setting::insert($rows->all());
        }
    }
}
