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

namespace Valkyrja\Notification\Notifications;

use Valkyrja\Broadcast\Message as BroadcastMessage;
use Valkyrja\Mail\Message as MailMessage;
use Valkyrja\Notification\Notification as Contract;
use Valkyrja\Sms\Message as SMSMessage;

/**
 * Abstract Class Notification.
 *
 * @author Melech Mizrachi
 */
abstract class Notification implements Contract
{
    /**
     * The broadcast adapter to use for this notification.
     *  Null ensures the default from config is used.
     */
    protected static ?string $broadcastAdapter = null;

    /**
     * The broadcast message to use for this notification.
     *  Null ensures the default from config is used.
     */
    protected static ?string $broadcastMessage = null;

    protected static bool $shouldSendBroadcast = true;

    /**
     * The mail adapter to use for this notification.
     *  Null ensures the default from config is used.
     */
    protected static ?string $mailAdapter = null;

    /**
     * The mail message to use for this notification.
     *  Null ensures the default from config is used.
     */
    protected static ?string $mailMessage = null;

    protected static bool $shouldSendMail = true;

    /**
     * The SMS adapter to use for this notification.
     *  Null ensures the default from config is used.
     */
    protected static ?string $smsAdapter = null;

    /**
     * The SMS message to use for this notification.
     *  Null ensures the default from config is used.
     */
    protected static ?string $smsMessage = null;

    protected static bool $shouldSendSms = true;

    /**
     * @inheritDoc
     */
    public function getBroadcastAdapterName(): ?string
    {
        return static::$broadcastAdapter;
    }

    /**
     * @inheritDoc
     */
    public function getBroadcastMessageName(): ?string
    {
        return static::$broadcastMessage;
    }

    /**
     * @inheritDoc
     */
    public function shouldSendBroadcastMessage(): bool
    {
        return static::$shouldSendBroadcast;
    }

    /**
     * @inheritDoc
     */
    public function getMailAdapterName(): ?string
    {
        return static::$mailAdapter;
    }

    /**
     * @inheritDoc
     */
    public function getMailMessageName(): ?string
    {
        return static::$mailMessage;
    }

    /**
     * @inheritDoc
     */
    public function shouldSendMailMessage(): bool
    {
        return static::$shouldSendMail;
    }

    /**
     * @inheritDoc
     */
    public function getSmsAdapterName(): ?string
    {
        return static::$smsAdapter;
    }

    /**
     * @inheritDoc
     */
    public function getSmsMessageName(): ?string
    {
        return static::$smsMessage;
    }

    /**
     * @inheritDoc
     */
    public function shouldSendSmsMessage(): bool
    {
        return static::$shouldSendSms;
    }

    /**
     * @inheritDoc
     */
    public function broadcast(BroadcastMessage $broadcastMessage): BroadcastMessage
    {
        return $broadcastMessage;
    }

    /**
     * @inheritDoc
     */
    public function mail(MailMessage $mailMessage): MailMessage
    {
        return $mailMessage;
    }

    /**
     * @inheritDoc
     */
    public function sms(SMSMessage $message): SMSMessage
    {
        return $message;
    }
}
