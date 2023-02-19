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

namespace Valkyrja\Broadcast\Config;

use Valkyrja\Config\Constants\ConfigKeyPart as CKP;
use Valkyrja\Config\Constants\EnvKey;
use Valkyrja\Manager\Config\MessageConfig as Model;

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
        CKP::DEFAULT         => EnvKey::BROADCAST_DEFAULT,
        CKP::DEFAULT_MESSAGE => EnvKey::BROADCAST_DEFAULT_MESSAGE,
        CKP::ADAPTER         => EnvKey::BROADCAST_ADAPTER,
        CKP::DRIVER          => EnvKey::BROADCAST_DRIVER,
        CKP::MESSAGE         => EnvKey::BROADCAST_MESSAGE,
        CKP::BROADCASTERS    => EnvKey::BROADCAST_BROADCASTERS,
        CKP::MESSAGES        => EnvKey::BROADCAST_MESSAGES,
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
     * @inheritDoc
     */
    public string $message;

    /**
     * The adapters.
     */
    public array $broadcasters;

    /**
     * @inheritDoc
     *
     * @var array[]
     */
    public array $messages;
}
