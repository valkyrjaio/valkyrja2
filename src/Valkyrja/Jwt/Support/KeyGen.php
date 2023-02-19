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

namespace Valkyrja\Jwt\Support;

use OpenSSLAsymmetricKey;
use SodiumException;
use Valkyrja\Jwt\Models\EdDsaKey;

/**
 * Class KeyGen.
 *
 * @author Melech Mizrachi
 */
class KeyGen
{
    /**
     * Get a public/private key pair to use for EdDSA.
     *
     * @throws SodiumException
     */
    public static function edDsa(): EdDsaKey
    {
        $keyPair = sodium_crypto_sign_keypair();

        $key = new EdDsaKey();

        $key->privateKey = base64_encode(sodium_crypto_sign_secretkey($keyPair));
        $key->publicKey  = base64_encode(sodium_crypto_sign_publickey($keyPair));

        return $key;
    }

    /**
     * Get an openssl private key via a file path and passphrase.
     *
     * @param string $privateKeyFile The private key file path
     * @param string $passphrase     The passphrase
     */
    public static function opensslPrivateKey(string $privateKeyFile, string $passphrase): OpenSSLAsymmetricKey
    {
        return openssl_pkey_get_private(
            file_get_contents($privateKeyFile),
            $passphrase
        );
    }

    /**
     * Get an openssl private key via a file path and passphrase.
     *
     * @param OpenSSLAsymmetricKey $privateKey The private key
     */
    public static function opensslPublicKey(OpenSSLAsymmetricKey $privateKey): OpenSSLAsymmetricKey
    {
        return openssl_pkey_get_details($privateKey)['key'];
    }
}
