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

namespace Valkyrja\Mail\Adapter;

use JsonException;
use Valkyrja\Log\Driver\Contract\Driver as Logger;
use Valkyrja\Mail\Adapter\Contract\LogAdapter as Contract;
use Valkyrja\Mail\Message\Contract\Message;
use Valkyrja\Type\BuiltIn\Support\Arr;

/**
 * Class LogAdapter.
 *
 * @author Melech Mizrachi
 */
class LogAdapter implements Contract
{
    /**
     * LogAdapter constructor.
     *
     * @param Logger               $logger The logger
     * @param array<string, mixed> $config The config
     */
    public function __construct(
        protected Logger $logger,
        protected array $config
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function send(Message $message): void
    {
        $this->logger->info(static::class . ' Send');
        $this->logger->info('From Name:');
        $this->logger->info($message->getFromName());
        $this->logger->info('From Email:');
        $this->logger->info($message->getFromEmail());
        $this->logger->info('Recipients:');
        $this->logger->info(Arr::toString($message->getRecipients()));
        $this->logger->info('ReplyTo Recipients:');
        $this->logger->info(Arr::toString($message->getReplyToRecipients()));
        $this->logger->info('Copy Recipients:');
        $this->logger->info(Arr::toString($message->getCopyRecipients()));
        $this->logger->info('Blind Copy Recipients:');
        $this->logger->info(Arr::toString($message->getBlindCopyRecipients()));
        $this->logger->info('Attachments:');
        $this->logger->info(Arr::toString($message->getAttachments()));
        $this->logger->info('Subject:');
        $this->logger->info($message->getSubject());
        $this->logger->info('Body:');
        $this->logger->info($message->getBody());
        $this->logger->info('Plain Body:');
        $this->logger->info($message->getPlainBody() ?? '');
        $this->logger->info('Is HTML:');
        $this->logger->info((string) $message->isHtml());
    }
}
