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
 * Interface MessageFactory.
 *
 * @author   Melech Mizrachi
 *
 * @template Adapter
 * @template Driver
 * @template Message
 *
 * @extends Factory<Adapter, Driver>
 */
interface MessageFactory extends Factory
{
    /**
     * Create a new message.
     *
     * @param class-string<Message> $name   The message
     * @param array                 $config The config
     * @param array                 $data   [optional] The data
     */
    public function createMessage(string $name, array $config, array $data = []): Message;
}
