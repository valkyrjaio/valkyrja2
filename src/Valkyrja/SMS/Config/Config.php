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

namespace Valkyrja\SMS\Config;

use Valkyrja\Config\Constants\ConfigKeyPart as CKP;
use Valkyrja\Config\Constants\EnvKey;
use Valkyrja\Support\Manager\Config\MessageConfig as Model;

/**
 * Class Config.
 *
 * @author Melech Mizrachi
 */
class Config extends Model
{
    /**
     * @inheritDoc
     */
    protected static array $envKeys = [
        CKP::DEFAULT         => EnvKey::SMS_DEFAULT,
        CKP::DEFAULT_MESSAGE => EnvKey::SMS_DEFAULT_MESSAGE,
        CKP::ADAPTER         => EnvKey::SMS_ADAPTER,
        CKP::DRIVER          => EnvKey::SMS_DRIVER,
        CKP::MESSAGE         => EnvKey::SMS_MESSAGE,
        CKP::MESSENGERS      => EnvKey::SMS_MESSENGERS,
        CKP::MESSAGES        => EnvKey::SMS_MESSAGES,
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
     * The messengers.
     *
     * @var array[]
     */
    public array $messengers;

    /**
     * @inheritDoc
     */
    public array $messages;
}
