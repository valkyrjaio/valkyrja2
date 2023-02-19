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

namespace Valkyrja\Application\Config;

use Valkyrja\Application\Support\Provider;
use Valkyrja\Config\Config as Model;
use Valkyrja\Config\Constants\ConfigKeyPart as CKP;
use Valkyrja\Config\Constants\EnvKey;

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
        CKP::ENV               => EnvKey::APP_ENV,
        CKP::DEBUG             => EnvKey::APP_DEBUG,
        CKP::URL               => EnvKey::APP_URL,
        CKP::TIMEZONE          => EnvKey::APP_TIMEZONE,
        CKP::VERSION           => EnvKey::APP_VERSION,
        CKP::KEY               => EnvKey::APP_KEY,
        CKP::EXCEPTION_HANDLER => EnvKey::APP_EXCEPTION_HANDLER,
        CKP::HTTP_KERNEL       => EnvKey::APP_HTTP_KERNEL,
        CKP::PROVIDERS         => EnvKey::APP_PROVIDERS,
    ];

    /**
     * The environment name.
     */
    public string $env;

    /**
     * Flag to enable debug.
     */
    public bool $debug;

    /**
     * The url.
     */
    public string $url;

    /**
     * The timezone.
     */
    public string $timezone;

    /**
     * The version.
     */
    public string $version;

    /**
     * The key.
     */
    public string $key;

    /**
     * The exception handler class.
     */
    public string $exceptionHandler;

    /**
     * The http kernel class.
     */
    public string $httpKernel;

    /**
     * Array of config providers.
     *  NOTE: Provider::deferred() is disregarded.
     *
     * @var Provider[]|string[]
     */
    public array $providers;
}
