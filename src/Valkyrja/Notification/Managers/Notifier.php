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

namespace Valkyrja\Notification\Managers;

use Valkyrja\Broadcast\Broadcast;
use Valkyrja\Mail\Mail;
use Valkyrja\Notification\Config\Config;
use Valkyrja\Notification\Factory;
use Valkyrja\Notification\NotifiableUser;
use Valkyrja\Notification\Notification;
use Valkyrja\Notification\Notifier as Contract;
use Valkyrja\Sms\Sms;

/**
 * Class Notifier.
 *
 * @author Melech Mizrachi
 */
class Notifier implements Contract
{
    /**
     * The mail recipients.
     *
     * @var array[]
     */
    protected array $mailRecipients = [];

    /**
     * The SMS recipients.
     *
     * @var array[]
     */
    protected array $smsRecipients = [];

    /**
     * The broadcast events.
     *
     * @var array[]
     */
    protected array $broadcastEvents = [];

    /**
     * Notifier constructor.
     *
     * @param Factory      $factory   The factory
     * @param Broadcast    $broadcast The broadcast
     * @param Mail         $mail      The mail service
     * @param Sms          $sms       The sms service
     * @param Config|array $config    The config
     */
    public function __construct(
        protected Factory $factory,
        protected Broadcast $broadcast,
        protected Mail $mail,
        protected Sms $sms,
        protected Config|array $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function createNotification(string $name, array $data = []): Notification
    {
        return $this->factory->createNotification($name, $data);
    }

    /**
     * @inheritDoc
     */
    public function addMailRecipient(string $email, string $name = ''): static
    {
        $this->mailRecipients[] = [
            'email' => $email,
            'name'  => $name,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addSmsRecipient(string $phoneNumber): static
    {
        $this->smsRecipients[] = [
            'to' => $phoneNumber,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBroadcastEvent(string $event): static
    {
        $this->broadcastEvents[] = [
            'event' => $event,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addUserRecipient(NotifiableUser $user): static
    {
        $this->addSmsUserRecipient($user);
        $this->addMailUserRecipient($user);
        $this->addBroadcastUserRecipient($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function notify(Notification $notification): void
    // public function notify(string $notificationName, array $data = []): void
    {
        // $notification = $this->getNotification($notificationName, $data);

        if ($notification->shouldSendBroadcastMessage()) {
            $this->notifyByBroadcast($notification);
        }

        if ($notification->shouldSendMailMessage()) {
            $this->notifyByMail($notification);
        }

        if ($notification->shouldSendSmsMessage()) {
            $this->notifyBySms($notification);
        }

        $this->resetRecipients();
    }

    /**
     * @inheritDoc
     */
    public function notifyUser(Notification $notification, NotifiableUser $user): void
    {
        $this->addUserRecipient($user);
        $this->notify($notification);
    }

    /**
     * @inheritDoc
     */
    public function notifyUsers(Notification $notification, NotifiableUser ...$users): void
    {
        foreach ($users as $user) {
            $this->addUserRecipient($user);
        }

        $this->notify($notification);
    }

    /**
     * Reset all the recipient arrays.
     */
    protected function resetRecipients(): void
    {
        $this->broadcastEvents = [];
        $this->mailRecipients  = [];
        $this->smsRecipients   = [];
    }

    /**
     * Add a user as an broadcast recipient.
     *
     * @param NotifiableUser $user The user
     */
    protected function addBroadcastUserRecipient(NotifiableUser $user): void
    {
        if ($user::hasSecretIdField()) {
            $this->broadcastEvents[] = [
                'event' => $user->__get($user::getSecretIdField()),
                'user'  => $user,
            ];
        }
    }

    /**
     * Add a user as a mail recipient.
     *
     * @param NotifiableUser $user The user
     */
    protected function addMailUserRecipient(NotifiableUser $user): void
    {
        $this->mailRecipients[] = [
            'email' => $user->__get($user::getEmailField()),
            'name'  => $user::hasNameField() ? $user->__get($user::getNameField()) : '',
            'user'  => $user,
        ];
    }

    /**
     * Add a user as an SMS recipient.
     *
     * @param NotifiableUser $user The user
     */
    protected function addSmsUserRecipient(NotifiableUser $user): void
    {
        if ($user::hasPhoneNumberField()) {
            $this->smsRecipients[] = [
                'to'   => $user->__get($user::getPhoneNumberField()),
                'user' => $user,
            ];
        }
    }

    /**
     * Send a notification by broadcast.
     *
     * @param Notification $notification The notification
     */
    protected function notifyByBroadcast(Notification $notification): void
    {
        $broadcast        = $this->broadcast;
        $broadcastAdapter = $broadcast->use($notification->getBroadcastAdapterName());
        $broadcastMessage = $notification->getBroadcastMessageName();

        foreach ($this->broadcastEvents as $broadcastEvent) {
            $message = $broadcast->createMessage($broadcastMessage);

            $message->setEvent($broadcastEvent['event']);
            $notification->broadcast($message);

            $broadcastAdapter->send($message);
        }
    }

    /**
     * Send a notification by mail.
     *
     * @param Notification $notification The notification
     */
    protected function notifyByMail(Notification $notification): void
    {
        $mail        = $this->mail;
        $mailAdapter = $mail->use($notification->getMailAdapterName());
        $mailMessage = $notification->getMailMessageName();

        foreach ($this->mailRecipients as $mailRecipient) {
            $message = $mail->createMessage($mailMessage);

            $message->addRecipient($mailRecipient['email'], $mailRecipient['name']);
            $notification->mail($message);

            $mailAdapter->send($message);
        }
    }

    /**
     * Send a notification by SMS.
     *
     * @param Notification $notification The notification
     */
    protected function notifyBySms(Notification $notification): void
    {
        $sms        = $this->sms;
        $smsAdapter = $sms->use($notification->getSmsAdapterName());
        $smsMessage = $notification->getSmsMessageName();

        foreach ($this->smsRecipients as $smsRecipient) {
            $message = $sms->createMessage($smsMessage);

            $message->setTo($smsRecipient['to']);
            $notification->sms($message);

            $smsAdapter->send($message);
        }
    }
}
