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

namespace Valkyrja\Mail\Config;

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
        CKP::FROM_ADDRESS,
        CKP::FROM_NAME,
        CKP::ADAPTER,
        CKP::ADAPTERS,
        CKP::MESSAGE,
        CKP::MESSAGES,
    ];

    /**
     * The model properties env keys.
     *
     * @var array
     */
    protected static array $envKeys = [
        CKP::FROM_ADDRESS => EnvKey::MAIL_FROM_ADDRESS,
        CKP::FROM_NAME    => EnvKey::MAIL_FROM_NAME,
        CKP::ADAPTER      => EnvKey::MAIL_ADAPTER,
        CKP::ADAPTERS     => EnvKey::MAIL_ADAPTERS,
        CKP::MESSAGE      => EnvKey::MAIL_MESSAGE,
        CKP::MESSAGES     => EnvKey::MAIL_MESSAGES,
    ];

    /**
     * The from address.
     *
     * @var string
     */
    public string $fromAddress;

    /**
     * The from name.
     *
     * @var string
     */
    public string $fromName;

    /**
     * The default adapter.
     *
     * @var string
     */
    public string $adapter;

    /**
     * The adapters.
     *
     * @var string[]
     */
    public array $adapters;

    /**
     * The default message.
     *
     * @var string
     */
    public string $message;

    /**
     * The message adapters.
     *
     * @var string[]
     */
    public array $messages;
}
