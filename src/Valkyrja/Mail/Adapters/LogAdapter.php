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

namespace Valkyrja\Mail\Adapters;

use JsonException;
use Valkyrja\Log\Driver as Logger;
use Valkyrja\Mail\LogAdapter as Contract;
use Valkyrja\Mail\Message;
use Valkyrja\Type\Support\Arr;

/**
 * Class LogAdapter.
 *
 * @author Melech Mizrachi
 */
class LogAdapter implements Contract
{
    /**
     * The log driver.
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
        $this->log->info('From Name:');
        $this->log->info($message->getFromName());
        $this->log->info('From Email:');
        $this->log->info($message->getFromEmail());
        $this->log->info('Recipients:');
        $this->log->info(Arr::toString($message->getRecipients()));
        $this->log->info('ReplyTo Recipients:');
        $this->log->info(Arr::toString($message->getReplyToRecipients()));
        $this->log->info('Copy Recipients:');
        $this->log->info(Arr::toString($message->getCopyRecipients()));
        $this->log->info('Blind Copy Recipients:');
        $this->log->info(Arr::toString($message->getBlindCopyRecipients()));
        $this->log->info('Attachments:');
        $this->log->info(Arr::toString($message->getAttachments()));
        $this->log->info('Subject:');
        $this->log->info($message->getSubject());
        $this->log->info('Body:');
        $this->log->info($message->getBody());
        $this->log->info('Plain Body:');
        $this->log->info($message->getPlainBody() ?? '');
        $this->log->info('Is HTML:');
        $this->log->info((string) $message->isHtml());
    }
}
