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

namespace Valkyrja\Cache\Config;

use Valkyrja\Config\Constants\ConfigKeyPart as CKP;
use Valkyrja\Config\Constants\EnvKey;
use Valkyrja\Manager\Config\Config as Model;

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
        CKP::DEFAULT => EnvKey::CACHE_DEFAULT,
        CKP::ADAPTER => EnvKey::CACHE_ADAPTER,
        CKP::DRIVER  => EnvKey::CACHE_DRIVER,
        CKP::STORES  => EnvKey::CACHE_STORES,
    ];

    /**
     * The cache stores.
     */
    public array $stores;
}
