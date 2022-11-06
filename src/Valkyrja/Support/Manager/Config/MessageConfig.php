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

namespace Valkyrja\Support\Manager\Config;

use Valkyrja\Config\Constants\ConfigKeyPart as CKP;
use Valkyrja\Support\Manager\Message;

/**
 * Class Config.
 *
 * @author Melech Mizrachi
 */
class MessageConfig extends Config
{
    /**
     * The default message.
     *
     * @var string
     */
    public string $defaultMessage = CKP::DEFAULT;

    /**
     * The default message class.
     *
     * @var string
     */
    public string $message = Message::class;

    /**
     * The messages.
     *
     * @var array[]
     */
    public array $messages = [
        CKP::DEFAULT => [
            CKP::MESSAGE => null,
        ],
    ];
}
