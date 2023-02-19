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

namespace Valkyrja\Crypt\Adapters;

use Exception;
use JsonException;
use SodiumException;
use Valkyrja\Crypt\Exceptions\CryptException;
use Valkyrja\Type\Support\Arr;
use Valkyrja\Type\Support\Obj;

use function bin2hex;
use function hex2bin;
use function json_decode;
use function random_bytes;
use function sodium_crypto_secretbox;
use function sodium_crypto_secretbox_open;
use function sodium_memzero;

use const JSON_THROW_ON_ERROR;
use const SODIUM_CRYPTO_SECRETBOX_MACBYTES;
use const SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

/**
 * Class SodiumAdapter.
 *
 * @author Melech Mizrachi
 */
class SodiumAdapter extends Adapter
{
    /**
     * @inheritDoc
     */
    public function isValidEncryptedMessage(string $encrypted): bool
    {
        try {
            $this->getDecoded($encrypted);

            return true;
        } catch (CryptException) {
            // Left empty to default to false
        }

        return false;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     * @throws SodiumException
     */
    public function encrypt(string $message, string $key = null): string
    {
        $key    = $this->getKeyAsBytes($key);
        $nonce  = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = bin2hex($nonce . sodium_crypto_secretbox($message, $nonce, $key));

        sodium_memzero($message);
        sodium_memzero($key);

        return $cipher;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     * @throws SodiumException
     */
    public function encryptArray(array $array, string $key = null): string
    {
        return $this->encrypt(Arr::toString($array), $key);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     * @throws SodiumException
     */
    public function encryptObject(object $object, string $key = null): string
    {
        return $this->encrypt(Obj::toString($object), $key);
    }

    /**
     * @inheritDoc
     *
     * @throws SodiumException
     */
    public function decrypt(string $encrypted, string $key = null): string
    {
        $key   = $this->getKeyAsBytes($key);
        $plain = $this->getDecodedPlain($this->getDecoded($encrypted), $key);

        sodium_memzero($key);

        return $plain;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     * @throws SodiumException
     */
    public function decryptArray(string $encrypted, string $key = null): array
    {
        return json_decode($this->decrypt($encrypted, $key), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     * @throws SodiumException
     */
    public function decryptObject(string $encrypted, string $key = null): object
    {
        return json_decode($this->decrypt($encrypted, $key), false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Get a decoded encrypted message.
     *
     * @param string $encrypted The encrypted message
     *
     * @throws CryptException
     */
    protected function getDecoded(string $encrypted): string
    {
        $decoded = hex2bin($encrypted);

        $this->validateDecoded($decoded);

        return $decoded;
    }

    /**
     * Validate a decoded encrypted message.
     *
     * @throws CryptException
     */
    protected function validateDecoded(string|false $decoded): void
    {
        $this->validateDecodedType($decoded);
        /** @var string $decoded */
        $this->validateDecodedStrLen($decoded);
    }

    /**
     * Validate a decoded encrypted message type.
     *
     * @throws CryptException
     */
    protected function validateDecodedType(string|false $decoded): void
    {
        if (! $this->isValidDecodedType($decoded)) {
            throw new CryptException('The encoding failed');
        }
    }

    /**
     * Check if a decoded encrypted message is a valid type.
     */
    protected function isValidDecodedType(string|false $decoded): bool
    {
        return $decoded !== false;
    }

    /**
     * Validate a decoded encrypted message string length.
     *
     * @throws CryptException
     */
    protected function validateDecodedStrLen(string $decoded): void
    {
        if (! $this->isValidDecodedStrLen($decoded)) {
            throw new CryptException('The message was truncated');
        }
    }

    /**
     * Validate a decoded encrypted message string length.
     */
    protected function isValidDecodedStrLen(string $decoded): bool
    {
        return mb_strlen($decoded, '8bit') > (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES);
    }

    /**
     * Get plain text from decoded encrypted string.
     *
     * @param string      $decoded The decoded encrypted message
     * @param string|null $key     The encryption key
     *
     * @throws CryptException
     * @throws SodiumException
     */
    protected function getDecodedPlain(string $decoded, string $key = null): string
    {
        if ($key === null) {
            throw new CryptException("Invalid ky `$key` provided");
        }

        $nonce      = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $cipherText = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open($cipherText, $nonce, $key);

        $this->validatePlainDecoded($plain);

        /** @var string $plain */
        sodium_memzero($cipherText);

        return $plain;
    }

    /**
     * Validate a plain text encrypted message.
     *
     * @throws CryptException
     */
    protected function validatePlainDecoded(bool|string $plain): void
    {
        if (! $this->isValidPlainDecoded($plain)) {
            throw new CryptException('The message was tampered with in transit');
        }
    }

    /**
     * Validate a plain text encrypted message.
     */
    protected function isValidPlainDecoded(bool|string $plain): bool
    {
        return $plain !== false;
    }

    /**
     * Get a key as bytes.
     *
     * @param string|null $key [optional] The key
     */
    protected function getKeyAsBytes(string $key = null): string
    {
        $key ??= $this->key;

        return hex2bin($key);
    }
}
