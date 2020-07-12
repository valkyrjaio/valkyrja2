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

namespace Valkyrja\Crypt\Encrypters;

use Exception;
use Valkyrja\Crypt\Encrypter;

use function base64_encode;
use function json_encode;
use function random_bytes;
use function sodium_crypto_secretbox;
use function sodium_memzero;

use const JSON_THROW_ON_ERROR;
use const SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

/**
 * Class SodiumEncrypter.
 *
 * @author Melech Mizrachi
 */
class SodiumEncrypter implements Encrypter
{
    /**
     * Encrypt a message.
     *
     * @param string $message The message to encrypt
     * @param string $key     The encryption key
     *
     * @throws Exception Random Bytes Failure
     *
     * @return string
     */
    public function encrypt(string $message, string $key): string
    {
        $nonce  = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = base64_encode($nonce . sodium_crypto_secretbox($message, $nonce, $key));

        sodium_memzero($message);
        sodium_memzero($key);

        return $cipher;
    }

    /**
     * Encrypt an array.
     *
     * @param array  $array The array to encrypt
     * @param string $key   The encryption key
     *
     * @throws Exception Random Bytes Failure
     *
     * @return string
     */
    public function encryptArray(array $array, string $key): string
    {
        return $this->encrypt(json_encode($array, JSON_THROW_ON_ERROR), $key);
    }

    /**
     * Encrypt a json array.
     *
     * @param object $object The object to encrypt
     * @param string $key    The encryption key
     *
     * @throws Exception Random Bytes Failure
     *
     * @return string
     */
    public function encryptObject(object $object, string $key): string
    {
        return $this->encrypt(json_encode($object, JSON_THROW_ON_ERROR), $key);
    }
}
