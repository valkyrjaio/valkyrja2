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

namespace Valkyrja\Broadcast\Adapters;

use JsonException;
use Valkyrja\Broadcast\Message;
use Valkyrja\Log\Driver as Logger;
use Valkyrja\Type\Support\Arr;

/**
 * Class LogAdapter.
 *
 * @author Melech Mizrachi
 */
class LogAdapter extends NullAdapter
{
    /**
     * The logger.
     */
    protected Logger $log;

    /**
     * The config.
     */
    protected array $config;

    /**
     * LogAdapter constructor.
     *
     * @param Logger $logger The logger
     * @param array  $config The config
     */
    public function __construct(Logger $logger, array $config)
    {
        $this->config = $config;
        $this->log    = $logger;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function send(Message $message): void
    {
        $this->log->info(static::class . ' Send');
        $this->log->info('Channel:');
        $this->log->info($message->getChannel());
        $this->log->info('Event:');
        $this->log->info($message->getEvent());
        $this->log->info('Data:');
        $this->log->info(Arr::toString($message->getData() ?? []));
        $this->log->info('Message:');
        $this->log->info($message->getMessage());
    }
}
