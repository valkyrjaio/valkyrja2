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
     * Array of properties in the model.
     *
     * @var array
     */
    protected static array $modelProperties = [
        CKP::DEFAULT,
        CKP::ADAPTERS,
        CKP::DISKS,
    ];

    /**
     * The model properties env keys.
     *
     * @var array
     */
    protected static array $envKeys = [
        CKP::DEFAULT  => EnvKey::FILESYSTEM_DEFAULT,
        CKP::ADAPTERS => EnvKey::FILESYSTEM_ADAPTERS,
        CKP::DISKS    => EnvKey::FILESYSTEM_DISKS,
    ];

    /**
     * The default adapter.
     *
     * @var string
     */
    public string $default;

    /**
     * The adapters.
     *
     * @var array
     */
    public array $adapters;

    /**
     * The disks.
     *
     * @var array
     */
    public array $disks;
}
