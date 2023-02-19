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

namespace Valkyrja\Manager;

/**
 * Interface MessageManager.
 *
 * @author   Melech Mizrachi
 *
 * @template Driver
 * @template Factory
 * @template Message
 *
 * @extends Manager<Driver, Factory>
 */
interface MessageManager extends Manager
{
    /**
     * Create a new message.
     *
     * @param string|null $name [optional] The name of the message
     * @param array       $data [optional] The data
     */
    public function createMessage(string $name = null, array $data = []): Message;
}
