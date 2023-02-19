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

namespace Valkyrja\Filesystem\Config;

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
        CKP::DEFAULT => EnvKey::FILESYSTEM_DEFAULT,
        CKP::ADAPTER => EnvKey::FILESYSTEM_ADAPTER,
        CKP::DRIVER  => EnvKey::FILESYSTEM_DRIVER,
        CKP::DISKS   => EnvKey::FILESYSTEM_DISKS,
    ];

    /**
     * @inheritDoc
     */
    public string $default;

    /**
     * @inheritDoc
     */
    public string $adapter;

    /**
     * @inheritDoc
     */
    public string $driver;

    /**
     * The disks.
     */
    public array $disks;
}
