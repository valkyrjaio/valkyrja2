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

use Valkyrja\Auth\MailableUser;

/**
 * Interface NotifiableUser.
 *
 * @author Melech Mizrachi
 */
interface NotifiableUser extends MailableUser
{
    /**
     * Whether this user entity has a name field.
     */
    public static function hasNameField(): bool;

    /**
     * Get the name field.
     */
    public static function getNameField(): string;

    /**
     * Whether this user entity has a phone number field.
     */
    public static function hasPhoneNumberField(): bool;

    /**
     * Get the phone number field.
     */
    public static function getPhoneNumberField(): string;

    /**
     * Whether this user entity has a secret id field.
     */
    public static function hasSecretIdField(): bool;

    /**
     * Get the secret id field.
     */
    public static function getSecretIdField(): string;
}
