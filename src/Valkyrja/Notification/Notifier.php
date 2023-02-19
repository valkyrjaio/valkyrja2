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

namespace Valkyrja\Notification;

/**
 * Interface Notifier.
 *
 * @author Melech Mizrachi
 */
interface Notifier
{
    /**
     * Create a new notification.
     *
     * @param string $name The notification name
     * @param array  $data [optional] The data to add to the notification
     */
    public function createNotification(string $name, array $data = []): Notification;

    /**
     * Add a mail recipient to send a notification to.
     *
     * @param string $email The email
     * @param string $name  [optional] The name
     */
    public function addMailRecipient(string $email, string $name = ''): static;

    /**
     * Add an SMS recipient to send a notification to.
     *
     * @param string $phoneNumber The phone number
     */
    public function addSmsRecipient(string $phoneNumber): static;

    /**
     * Add a broadcast event to send a notification to.
     *
     * @param string $event The event
     */
    public function addBroadcastEvent(string $event): static;

    /**
     * Add a User recipient to send a notification to.
     */
    public function addUserRecipient(NotifiableUser $user): static;

    /**
     * Send a notification to recipients.
     *
     * @param Notification $notification The notification to send
     */
    public function notify(Notification $notification): void;

    /**
     * Send a notification to a user.
     *
     * @param Notification   $notification The notification to send
     * @param NotifiableUser $user         The user to notify
     */
    public function notifyUser(Notification $notification, NotifiableUser $user): void;

    /**
     * Send a notification to users.
     *
     * @param Notification     $notification The notification to send
     * @param NotifiableUser[] $users        The users to notify
     */
    public function notifyUsers(Notification $notification, NotifiableUser ...$users): void;
}
