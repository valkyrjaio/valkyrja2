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

namespace Valkyrja\Config\Config;

use Valkyrja\Annotation\Config\Config as Annotation;
use Valkyrja\Api\Config\Config as Api;
use Valkyrja\Application\Config\Config as App;
use Valkyrja\Asset\Config\Config as Asset;
use Valkyrja\Auth\Config\Config as Auth;
use Valkyrja\Broadcast\Config\Config as Broadcast;
use Valkyrja\Cache\Config\Config as Cache;
use Valkyrja\Client\Config\Config as Client;
use Valkyrja\Config\Config as Model;
use Valkyrja\Config\Constants\ConfigKeyPart as CKP;
use Valkyrja\Config\Constants\EnvKey;
use Valkyrja\Config\Support\Provider;
use Valkyrja\Console\Config\Config as Console;
use Valkyrja\Container\Config\Config as Container;
use Valkyrja\Crypt\Config\Config as Crypt;
use Valkyrja\Event\Config\Config as Event;
use Valkyrja\Filesystem\Config\Config as Filesystem;
use Valkyrja\Jwt\Config\Config as Jwt;
use Valkyrja\Log\Config\Config as Log;
use Valkyrja\Mail\Config\Config as Mail;
use Valkyrja\Notification\Config\Config as Notification;
use Valkyrja\Orm\Config\Config as ORM;
use Valkyrja\Path\Config\Config as Path;
use Valkyrja\Routing\Config\Config as Routing;
use Valkyrja\Session\Config\Config as Session;
use Valkyrja\Sms\Config\Config as Sms;
use Valkyrja\Validation\Config\Config as Validation;
use Valkyrja\View\Config\Config as View;

/**
 * Class Config.
 *
 * @author Melech Mizrachi
 */
class Config extends Model
{
    /**
     * @inheritDoc
     *
     * @var array<string, string>
     */
    protected static array $envKeys = [
        CKP::PROVIDERS       => EnvKey::CONFIG_PROVIDERS,
        CKP::CACHE_FILE_PATH => EnvKey::CONFIG_CACHE_FILE_PATH,
        CKP::USE_CACHE       => EnvKey::CONFIG_USE_CACHE_FILE,
    ];

    /**
     * The annotation module config.
     */
    public Annotation $annotation;

    /**
     * The api module config.
     */
    public Api $api;

    /**
     * The application module config.
     */
    public App $app;

    /**
     * The asset module config.
     */
    public Asset $asset;

    /**
     * The auth module config.
     */
    public Auth $auth;

    /**
     * The broadcast module config.
     */
    public Broadcast $broadcast;

    /**
     * The cache module config.
     */
    public Cache $cache;

    /**
     * The client module config.
     */
    public Client $client;

    /**
     * The console module config.
     */
    public Console $console;

    /**
     * The container module config.
     */
    public Container $container;

    /**
     * The crypt module config.
     */
    public Crypt $crypt;

    /**
     * The event module config.
     */
    public Event $event;

    /**
     * The filesystem module config.
     */
    public Filesystem $filesystem;

    /**
     * The Jwt module config.
     */
    public Jwt $jwt;

    /**
     * The logging module config.
     */
    public Log $log;

    /**
     * The mail module config.
     */
    public Mail $mail;

    /**
     * The notification module config.
     */
    public Notification $notification;

    /**
     * The ORM module config.
     */
    public ORM $orm;

    /**
     * The path module config.
     */
    public Path $path;

    /**
     * The routing module config.
     */
    public Routing $routing;

    /**
     * The session module config.
     */
    public Session $session;

    /**
     * The SMS module config.
     */
    public SMS $sms;

    /**
     * The validation module config.
     */
    public Validation $validation;

    /**
     * The view module config.
     */
    public View $view;

    /**
     * Array of config providers.
     *  NOTE: Provider::deferred() is disregarded.
     *
     * @var class-string<Provider>[]
     */
    public array $providers;

    /**
     * The cache file path.
     */
    public string $cacheFilePath;

    /**
     * Whether to use cache.
     */
    public bool $useCache;
}
