<?php

namespace App\Http\Controllers\Admin;

use App\Actions\SettingsUploads;
use App\Enums\MailDrivers;
use App\Enums\SmsDrivers;
use App\Gateways\Payment\Facades\Payment;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Site as Sitemap;
use File;
use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;


class SettingsController extends Controller
{
    /**
     * Display site settings editing page
     * @return \Illuminate\View\View
     */
    public function site(Request $request)
    {
        $settings = settings()->for('site')->all();
        $settings['logo_uri'] = $settings['logo'];
        $zones = File::get(resource_path('js/constants/timezones.json'));
        $timezones = json_decode($zones);
        return Inertia::render('Admin/Settings/Site', compact('settings', 'timezones'));
    }

    /**
     * Display socials login Editing page
     * @return \Illuminate\View\View
     */
    public function social(Request $request)
    {
        $social = [
            'enableGithub' => str(settings('github.enable'))->toBoolean(),
            'githubClientId' => settings('github.client_id'),
            'githubClientSecret' => settings('github.client_secret'),
            'githubRedirect' => settings('github.redirect'),
            'enableGoogle' => str(settings('google.enable'))->toBoolean(),
            'googleClientId' => settings('google.client_id'),
            'googleClientSecret' => settings('google.client_secret'),
            'googleProjectId' => settings('google.project_id'),
            'enableFacebook' => str(settings('facebook.enable'))->toBoolean(),
            'facebookClientId' => settings('facebook.client_id'),
            'facebookClientSecret' => settings('facebook.client_secret'),
            'facebookRedirect' => settings('facebook.redirect'),
        ];
        return Inertia::render('Admin/Settings/SocialLogin', compact('social'));
    }

    /**
     * Display meta Editing page.
     * @return \Illuminate\View\View
     */
    public function meta(Request $request)
    { // sitemap

        return Inertia::render('Admin/Settings/SiteMeta', [
            'meta' => [
                'sitemapLeagues' =>  settings('sitemap.leagues'),
                'sitemapGames' =>  settings('sitemap.games'),
                'sitemapEnableSitemap' => str(settings('sitemap.enable_sitemap'))->toBoolean(),
                'sitemapEnableMeta' => str(settings('sitemap.enable_meta'))->toBoolean(),
                'homeTitle' =>  settings('home.title'),
                'homeKeywords' =>  settings('home.keywords'),
                'homeDescription' =>  settings('home.description'),
                'gamesTitle' =>  settings('games.title'),
                'gamesKeywords' =>  settings('games.keywords'),
                'gamesDescription' =>  settings('games.description'),
                'gameTitle' =>  settings('game.title'),
                'gameKeywords' =>  settings('game.keywords'),
                'gameDescription' =>  settings('game.description'),
            ]
        ]);
    }

    /**
     * Edit update form for privacy and policy
     * @return \Illuminate\View\View
     */
    public function privacyTerms(Request $request)
    {
        $pages = [
            'privacy' => settings('pages.privacy'),
            'terms' => settings('pages.terms'),
            'gdprNotice' => settings('pages.gdpr_notice'),
            'gdprTerms' => settings('pages.gdpr_terms'),
        ];
        return Inertia::render('Admin/Settings/PrivacyPolicy', compact('pages'));
    }

    /**
     * Display notification editing page.
     * @return \Illuminate\View\View
     */
    public function notifications(Request $request)
    { // sitemap

        return Inertia::render(
            'Admin/Settings/Notifications',
            [
                'smsDrivers' => collect(SmsDrivers::cases())->map(fn (SmsDrivers $d) => $d->info()),
                'mailDrivers' => collect(MailDrivers::cases())->map(fn (MailDrivers $d) => $d->info()),
                ...settings()->for([
                    'smspoh',
                    'touchsms',
                    'clickatell',
                    'messagebird',
                    'vonage',
                    'notifications',
                    'textmagic',
                    'mail',
                    'sms',
                    'smtp',
                    'mailgun',
                    'postmark'
                ])->all()
            ]
        );
    }

    /**
     * Display payments page
     * @return \Illuminate\View\View
     */
    public function payments(Request $request)
    { // sitemap\
        return Inertia::render(
            'Admin/Settings/Payments',
            ['gateways' => collect(Payment::gateways())->flatMap(function ($driver) {
                return [$driver => Payment::driver($driver)->getConfig()];
            })]
        );
    }

    /**
     * Store site settings
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $settings = $request->except(['group', 'logo_uri', 'logo_path', 'logo_upload']);
        $group = $request->input('group');
        foreach ($settings as $name => $val) {
            if (in_array($name, ['enable_bookie', 'enable_exchange', 'enable_kyc'])) {
                $val = $request->boolean($name) ? 'true' : 'false';
            }
            Setting::query()
                ->where('name', $name)
                ->where('group', $group)
                ->update(['val' => $val]);
        }
        if ($request->has('logo_upload') && $request->filled('logo_uri')) {
            $setting = Setting::where('name', 'logo')->where('group', $group)->first();
            app(SettingsUploads::class)->upload($request, $setting, 'logo');
        }
        settings()->refresh();
        return back()->with('success', __('Changes to Settings saved!'));
    }


    /**
     * Store social settings
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeSocial(Request $request)
    {
        settings()->set('google.enable', $request->boolean('enableGoogle') ? 'true' : 'false');
        settings()->set('github.enable', $request->boolean('enableGithub') ? 'true' : 'false');
        settings()->set('github.client_id', $request->githubClientId);
        settings()->set('github.client_secret', $request->githubClientSecret);
        settings()->set('github.redirect', $request->githubRedirect);
        settings()->set('google.enable', $request->boolean('enableGoogle') ? 'true' : 'false');
        settings()->set('google.client_id', $request->googleClientId);
        settings()->set('google.client_secret', $request->googleClientSecret);
        settings()->set('google.project_id', $request->googleProjectId);
        settings()->set('facebook.enable', $request->boolean('enableFacebook') ? 'true' : 'false');
        settings()->set('facebook.client_id', $request->facebookClientId);
        settings()->set('facebook.client_secret', $request->facebookClientSecret);
        settings()->set('facebook.redirect', $request->facebookRedirect);
        settings()->refresh();
        return back()->with('success', __('Changes to Social Login Settings saved!'));
    }

    /**
     * Store meta settings
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeMeta(Request $request)
    {

        settings()->set('sitemap.enable_sitemap', $request->boolean('sitemapEnableSitemap') ? 'true' : 'false');
        settings()->set('sitemap.enable_meta', $request->boolean('sitemapEnableMeta') ? 'true' : 'false');
        settings()->set('github.client_id', $request->githubClientId);
        settings()->set('sitemap.leagues', $request->sitemapLeagues);
        settings()->set('sitemap.games', $request->sitemapGames);
        settings()->set('home.title', $request->homeTitle);
        settings()->set('home.keywords', $request->homeKeywords);
        settings()->set('home.description', $request->homeDescription);
        settings()->set('games.title', $request->gamesTitle);
        settings()->set('games.keywords', $request->gamesKeywords);
        settings()->set('games.description', $request->gamesDescription);
        settings()->set('game.title', $request->gameTitle);
        settings()->set('game.keywords', $request->gameKeywords);
        settings()->set('game.description', $request->gameDescription);
        settings()->refresh();
        return back()->with('success', __('Changes to Meta Settings saved!'));
    }

    /**
     * Store privacy settings
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storePrivacyTerms(Request $request)
    {
        settings()->set('pages.privacy', $request->privacy);
        settings()->set('pages.terms', $request->terms);
        settings()->set('pages.gdpr_notice', $request->gdprNotice);
        settings()->set('pages.gdpr_terms', $request->gdprTerms);
        settings()->refresh();
        return back()->with('success', __('Changes to Privacy Settings saved!'));
    }

    /**
     * Store notification settings
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeNotifications(Request $request)
    {
        settings()->set('notifications.enable_sms', $request->boolean('enable_sms') ? 'true' : 'false');
        settings()->set('notifications.enable_email', $request->boolean('enable_email') ? 'true' : 'false');
        settings()->set('notifications.mailer', $request->mailer);
        settings()->set('notifications.sms', $request->sms);
        settings()->set('notifications.from_address', $request->from_address);
        settings()->set('notifications.from_name', $request->from_name);
        settings()->refresh();
        return back()->with('success', __('Changes to Notification Settings saved!'));
    }


    /**
     * Store Mail settings
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeMail(Request $request)
    {
        $request->validate(['provider' => ['required', 'string', new Enum(MailDrivers::class)]]);
        $provider = MailDrivers::from($request->provider);
        settings()->set('notifications.mailer', $request->provider);
        switch ($provider) {
            case MailDrivers::mailgun:
                settings()->set('mailgun.secret', $request->secret);
                settings()->set('mailgun.domain', $request->domain);
                break;
            case MailDrivers::mailjet:
                settings()->set('mailjet.key', $request->key);
                settings()->set('mailjet.secret', $request->secret);
                break;
            case MailDrivers::smtp:
                settings()->set('smtp.host', $request->host);
                settings()->set('smtp.port', $request->port);
                settings()->set('smtp.username', $request->username);
                settings()->set('smtp.password', $request->password);
                settings()->set('smtp.encryption', $request->encryption);
                break;
            case MailDrivers::postmark:
                settings()->set('postmark.token', $request->token);
                break;
            case MailDrivers::sendgrid:
                settings()->set('sendgrid.key', $request->key);
                break;
        }
        settings()->refresh();
        return back()->with('success', __('Changes to site mailer saved!'));
    }

    /**
     * Store sms settings
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeSMS(Request $request)
    {
        $request->validate(['provider' => ['required', 'string', new Enum(SmsDrivers::class)]]);
        $provider = SmsDrivers::from($request->provider);
        settings()->set('notifications.sms', $request->provider);
        switch ($provider) {
            case SmsDrivers::clickatell:
                settings()->set('clickatell.apiKey', $request->apiKey);
                break;
            case SmsDrivers::messagebird:
                settings()->set('messagebird.access_key', $request->access_key);
                settings()->set('messagebird.originator', $request->originator);
                break;
            case SmsDrivers::smspoh:
                settings()->set('smspoh.endpoint', $request->endpoint);
                settings()->set('smspoh.token', $request->token);
                settings()->set('smspoh.sender', $request->sender);
                break;
            case SmsDrivers::textmagic:
                settings()->set('textmagic.apiv2_key', $request->apiv2_key);
                settings()->set('textmagic.username', $request->username);
                break;
            case SmsDrivers::touchsms:
                settings()->set('touchsms.token_id', $request->token_id);
                settings()->set('touchsms.access_token', $request->access_token);
                settings()->set('touchsms.default_sender', $request->default_sender);
                break;
            case SmsDrivers::vonage:
                settings()->set('vonage.api_key', $request->api_key);
                settings()->set('vonage.api_secret', $request->api_secret);
                settings()->set('vonage.sms_sender', $request->sms_sender);
                break;
        }
        settings()->refresh();
        return back()->with('success', __('Changes to site mailer saved!'));
    }

    /**
     * Store System Notification  Messages
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeMessages(Request $request)
    {
        $request->validate(['notification' => [
            'required',
            'string',
            'in:balance_deposit,balance_withdraw,bet_lost,bet_refunded,bet_won,kyc_completed,kyc_denied'
        ]]);
        $notification = $request->notification;
        settings()->set("mail.{$notification}_message", $request->message);
        settings()->set("mail.{$notification}_subject", $request->subject);
        settings()->set("mail.{$notification}_salutation", $request->salutation);
        settings()->set("sms.{$notification}_message", $request->sms);
        settings()->refresh();
        return back()->with('success', __('Changes to mail message saved!'));
    }

    /**
     * Store payment config
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storePayments(Request $request)
    {
        $gateways = implode(",", Payment::gateways());
        $request->validate(['gateway' => ['required', 'string', "in:$gateways"]]);
        $gateway = $request->gateway;
        $settings = Payment::driver($gateway)->setConfig($request);
        return back()->with('success', __('Changes to :gateway settings saved!', ['gateway' => $settings['name']]));
    }

    /**
     * Generate a fresh sitemap for the website
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function generateSitemap()
    {
        app(Sitemap::class)->generate();
        return back()->with('success', __('A new sitemap was generated'));
    }

    /**
     * Remove the current sitemap
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroySitemap()
    {
        $removed = app(Sitemap::class)->destroy();
        return $removed
            ? back()->with('success', __('Sitemap was removed successfully'))
            : back()->with('error', __('Sitemap was not found or failed to be deleted'));
    }
}
