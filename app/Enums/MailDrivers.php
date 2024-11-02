<?php

namespace App\Enums;

use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Arr;
use Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunTransportFactory;
use Symfony\Component\Mailer\Bridge\Postmark\Transport\PostmarkTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Bridge\Mailjet\Transport\MailjetTransportFactory;
use Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridTransportFactory;

enum MailDrivers: string
{


    case smtp = 'smtp';
    case mailgun = 'mailgun';
    case postmark = 'postmark';
    case mailjet = 'mailjet';
    case sendgrid = 'sendgrid';

    public function driver()
    {
        return match ($this) {
            static::smtp => function () {
                $config = settings()->for('smtp')->all();
                return $this->createSmtpTransport($config);
            },
            static::mailgun => function () {
                $config = settings()->for('mailgun')->all();
                return $this->createMailgunTransport($config);
            },
            static::postmark => function () {
                $config = settings()->for('postmark')->all();
                return $this->createPostmarkTransport($config);
            },
            static::mailjet => function () {
                $config = settings()->for('mailjet')->all();
                return (new MailjetTransportFactory(null, $this->getHttpClient($config)))
                    ->create(
                        new Dsn(
                            'mailjet+api',
                            'default',
                            $config['key'],
                            $config['secret']
                        )
                    );
            },
            static::sendgrid => function () {
                $config = settings()->for('sendgrid')->all();
                return (new SendGridTransportFactory(null, $this->getHttpClient($config)))
                    ->create(
                        new Dsn(
                            'sendgrid+api',
                            'default',
                            $config['key'],
                        )
                    );
            },
        };
    }
    /**
     * Create an instance of the Symfony SMTP Transport driver.
     *
     * @param  array  $config
     * @return \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport
     */
    protected function createSmtpTransport(array $config)
    {
        $factory = new EsmtpTransportFactory;
        $scheme = $config['scheme'] ?? null;
        if (!$scheme) {
            $scheme = !empty($config['encryption']) && $config['encryption'] == 'tls'
                ? (($config['port'] == 465) ? 'smtps' : 'smtp')
                : '';
        }
        return $factory->create(new Dsn(
            $scheme,
            $config['host'],
            $config['username'] ?? null,
            $config['password'] ?? null,
            $config['port'] ?? null,
            $config
        ));
    }

    /**
     * Create an instance of the Symfony Mailgun Transport driver.
     *
     * @param  array  $config
     * @return \Symfony\Component\Mailer\Transport\TransportInterface
     */
    protected function createMailgunTransport(array $config)
    {
        $factory = new MailgunTransportFactory(null, $this->getHttpClient($config));
        if (!isset($config['secret'])) {
            $config = $this->app['config']->get('services.mailgun', []);
        }
        return $factory->create(new Dsn(
            'mailgun+' . ($config['scheme'] ?? 'https'),
            $config['endpoint'] ?? 'default',
            $config['secret'],
            $config['domain']
        ));
    }

    /**
     * Create an instance of the Symfony Postmark Transport driver.
     *
     * @param  array  $config
     * @return \Symfony\Component\Mailer\Bridge\Postmark\Transport\PostmarkApiTransport
     */
    protected function createPostmarkTransport(array $config)
    {
        $factory = new PostmarkTransportFactory(null, $this->getHttpClient($config));

        $options = isset($config['message_stream_id'])
            ? ['message_stream' => $config['message_stream_id']]
            : [];

        return $factory->create(new Dsn(
            'postmark+api',
            'default',
            $config['token'] ?? $this->app['config']->get('services.postmark.token'),
            null,
            null,
            $options
        ));
    }

    /**
     * Get a configured Symfony HTTP client instance.
     *
     * @return \Symfony\Contracts\HttpClient\HttpClientInterface|null
     */
    protected function getHttpClient(array $config)
    {
        if ($options = ($config['client'] ?? false)) {
            $maxHostConnections = Arr::pull($options, 'max_host_connections', 6);
            $maxPendingPushes = Arr::pull($options, 'max_pending_pushes', 50);
            return HttpClient::create($options, $maxHostConnections, $maxPendingPushes);
        }
    }

    public function info()
    {

        return match ($this) {
            static::smtp => [
                'label' => 'SMTP',
                'value' => $this->value
            ],
            static::mailgun  => [
                'label' => 'Mailgun',
                'img' => 'https://images.ctfassets.net/y6oq7udscnj8/6bfhvqjWqiC8dCzBNHxtJP/d682492374473b2e0d1377f0d4247bda/MG-Icon.png',
                'value' => $this->value
            ],
            static::postmark  => [
                'label' => 'Postmark',
                'img' => 'https://postmarkapp.com/images/apple-touch-icon.png',
                'value' => $this->value
            ],
            static::mailjet  => [
                'label' => 'Mailjet',
                'img' => 'https://images.ctfassets.net/y6oq7udscnj8/34r8FUOtvd8lXdEDteTxSG/b38a5e52f36a4e5835d325bec79c4ddf/MJ-Icon.png',
                'value' => $this->value
            ],
            static::sendgrid  => [
                'label' => 'Send Grid',
                'img' => 'https://sendgrid.com/content/dam/sendgrid/core-assets/social/apple-touch-icon.png',
                'value' => $this->value
            ]
        };
    }
}
