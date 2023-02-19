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

namespace Valkyrja\Sms\Adapters;

use Valkyrja\Log\Driver as Logger;
use Valkyrja\Sms\Driver;
use Valkyrja\Sms\LogAdapter as Contract;
use Valkyrja\Sms\Message;

/**
 * Class LogAdapter.
 *
 * @author Melech Mizrachi
 */
class LogAdapter implements Contract
{
    /**
     * The log adapter.
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
     * @param array{
     *     adapter: class-string<static>|null,
     *     driver: class-string<Driver>|null,
     *     logger: class-string<Logger>
     * }             $config The config
     */
    public function __construct(Logger $logger, array $config)
    {
        $this->config = $config;
        $this->log    = $logger;
    }

    /**
     * @inheritDoc
     */
    public function send(Message $message): void
    {
        $this->log->info(static::class . ' Send');
        $this->log->info('From:');
        $this->log->info($message->getFrom());
        $this->log->info('To:');
        $this->log->info($message->getTo());
        $this->log->info('Text:');
        $this->log->info($message->getText());
    }
}
