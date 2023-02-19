<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja Framework package.
 *
 * (c) Melech Mizrachi <melechmizrachi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Mail\Providers;

use GuzzleHttp\Client;
use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Mailgun;
use PHPMailer\PHPMailer\PHPMailer;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Log\Logger;
use Valkyrja\Mail\Adapter;
use Valkyrja\Mail\Driver;
use Valkyrja\Mail\Factories\ContainerFactory;
use Valkyrja\Mail\Factory;
use Valkyrja\Mail\LogAdapter;
use Valkyrja\Mail\Mail;
use Valkyrja\Mail\MailgunAdapter;
use Valkyrja\Mail\Message;
use Valkyrja\Mail\PHPMailerAdapter;

/**
 * Class ServiceProvider.
 *
 * @author Melech Mizrachi
 */
class ServiceProvider extends Provider
{
    /**
     * @inheritDoc
     */
    public static function publishers(): array
    {
        return [
            Mail::class             => 'publishMail',
            Factory::class          => 'publishFactory',
            Driver::class           => 'publishDriver',
            Adapter::class          => 'publishAdapter',
            LogAdapter::class       => 'publishLogAdapter',
            PHPMailerAdapter::class => 'publishPHPMailerAdapter',
            MailgunAdapter::class   => 'publishMailgunAdapter',
            PHPMailer::class        => 'publishPHPMailer',
            Mailgun::class          => 'publishMailgun',
            Message::class          => 'publishMessage',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Mail::class,
            Factory::class,
            Driver::class,
            Adapter::class,
            LogAdapter::class,
            PHPMailerAdapter::class,
            MailgunAdapter::class,
            PHPMailer::class,
            Mailgun::class,
            Message::class,
        ];
    }

    /**
     * Publish the mail service.
     *
     * @param Container $container The container
     */
    public static function publishMail(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Mail::class,
            new \Valkyrja\Mail\Managers\Mail(
                $container->getSingleton(Factory::class),
                $config['mail']
            )
        );
    }

    /**
     * Publish the factory service.
     *
     * @param Container $container The container
     */
    public static function publishFactory(Container $container): void
    {
        $container->setSingleton(
            Factory::class,
            new ContainerFactory($container),
        );
    }

    /**
     * Publish the default driver service.
     *
     * @param Container $container The container
     */
    public static function publishDriver(Container $container): void
    {
        $container->setClosure(
            Driver::class,
            /**
             * @param class-string<Driver> $name
             */
            static function (string $name, Adapter $adapter): Driver {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish an adapter service.
     *
     * @param Container $container The container
     */
    public static function publishAdapter(Container $container): void
    {
        $container->setClosure(
            Adapter::class,
            /**
             * @param class-string<Adapter> $name
             */
            static function (string $name, array $config): Adapter {
                return new $name(
                    $config
                );
            }
        );
    }

    /**
     * Publish the log adapter service.
     *
     * @param Container $container The container
     */
    public static function publishLogAdapter(Container $container): void
    {
        $logger = $container->getSingleton(Logger::class);

        $container->setClosure(
            LogAdapter::class,
            /**
             * @param class-string<LogAdapter> $name
             */
            static function (string $name, array $config) use ($logger): LogAdapter {
                return new $name(
                    $logger->use($config['logger'] ?? null),
                    $config
                );
            }
        );
    }

    /**
     * Publish the PHP Mailer adapter service.
     *
     * @param Container $container The container
     */
    public static function publishPHPMailerAdapter(Container $container): void
    {
        $container->setClosure(
            PHPMailerAdapter::class,
            /**
             * @param class-string<PHPMailerAdapter> $name
             */
            static function (string $name, array $config) use ($container): PHPMailerAdapter {
                return new $name(
                    $container->get(PHPMailer::class, [$config])
                );
            }
        );
    }

    /**
     * Publish the PHP Mailer service.
     *
     * @param Container $container The container
     */
    public static function publishPHPMailer(Container $container): void
    {
        $globalConfig = $container->getSingleton(Config::class);
        $appDebug     = $globalConfig['app']['debug'] ?? null;

        $container->setClosure(
            PHPMailer::class,
            static function (array $config) use ($appDebug): PHPMailer {
                // Create a new instance of the PHPMailer class
                $mailer = new PHPMailer(true);

                // Enable verbose debug output
                $mailer->SMTPDebug = $appDebug ? 2 : 0;
                // Set mailer to use SMTP
                $mailer->isSMTP();
                // Specify main and backup SMTP servers
                $mailer->Host = $config['host'];
                // SMTP Port
                $mailer->Port = $config['port'];
                // Enable SMTP authentication
                $mailer->SMTPAuth = true;
                // SMTP username
                $mailer->Username = $config['username'];
                // SMTP password
                $mailer->Password = $config['password'];
                // Enable TLS encryption, `ssl` also accepted
                $mailer->SMTPSecure = $config['encryption'];

                return $mailer;
            }
        );
    }

    /**
     * Publish the Mailgun adapter service.
     *
     * @param Container $container The container
     */
    public static function publishMailgunAdapter(Container $container): void
    {
        $container->setClosure(
            MailgunAdapter::class,
            /**
             * @param class-string<MailgunAdapter> $name
             */
            static function (string $name, array $config) use ($container): MailgunAdapter {
                return new $name(
                    $container->get(Mailgun::class, [$config]),
                    $config
                );
            }
        );
    }

    /**
     * Publish the Mailgun service.
     *
     * @param Container $container The container
     */
    public static function publishMailgun(Container $container): void
    {
        $container->setClosure(
            Mailgun::class,
            static function (array $config): Mailgun {
                $httpClientConfigurator = (new HttpClientConfigurator())
                    ->setApiKey($config['apiKey'])
                    ->setHttpClient(new Client());

                return new Mailgun(
                    $httpClientConfigurator
                );
            }
        );
    }

    /**
     * Publish the message service.
     *
     * @param Container $container The container
     */
    public static function publishMessage(Container $container): void
    {
        $container->setClosure(
            Message::class,
            static function (string $name, array $config): Message {
                return (new $name())->setFrom($config['fromAddress'], $config['fromName']);
            }
        );
    }
}
