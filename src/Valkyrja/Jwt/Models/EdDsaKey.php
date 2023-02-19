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

namespace Valkyrja\Jwt\Models;

use Valkyrja\Model\Models\Model;

/**
 * Class EdDsaKey.
 *
 * @author Melech Mizrachi
 */
class EdDsaKey extends Model
{
    /**
     * The private key.
     */
    public string $privateKey;

    /**
     * The public key.
     */
    public string $publicKey;
}
