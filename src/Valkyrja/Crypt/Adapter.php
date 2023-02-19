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

namespace Valkyrja\Crypt;

use Valkyrja\Crypt\Exceptions\CryptException;
use Valkyrja\Manager\Adapter as Contract;

/**
 * Interface Adapter.
 *
 * @author Melech Mizrachi
 */
interface Adapter extends Contract
{
    /**
     * Determine if an encrypted message is valid.
     */
    public function isValidEncryptedMessage(string $encrypted): bool;

    /**
     * Encrypt a message.
     *
     * @param string      $message The message to encrypt
     * @param string|null $key     The encryption key
     *
     * @throws CryptException On any failure
     */
    public function encrypt(string $message, string $key = null): string;

    /**
     * Encrypt an array.
     *
     * @param array       $array The array to encrypt
     * @param string|null $key   The encryption key
     *
     * @throws CryptException On any failure
     */
    public function encryptArray(array $array, string $key = null): string;

    /**
     * Encrypt a json array.
     *
     * @param object      $object The object to encrypt
     * @param string|null $key    The encryption key
     *
     * @throws CryptException On any failure
     */
    public function encryptObject(object $object, string $key = null): string;

    /**
     * Decrypt a message.
     *
     * @param string      $encrypted The encrypted message to decrypt
     * @param string|null $key       The encryption key
     *
     * @throws CryptException On any failure
     */
    public function decrypt(string $encrypted, string $key = null): string;

    /**
     * Decrypt a message originally encrypted from an array.
     *
     * @param string      $encrypted The encrypted message
     * @param string|null $key       The encryption key
     *
     * @throws CryptException On any failure
     */
    public function decryptArray(string $encrypted, string $key = null): array;

    /**
     * Decrypt a message originally encrypted from an object.
     *
     * @param string      $encrypted The encrypted message
     * @param string|null $key       The encryption key
     *
     * @throws CryptException On any failure
     */
    public function decryptObject(string $encrypted, string $key = null): object;
}
