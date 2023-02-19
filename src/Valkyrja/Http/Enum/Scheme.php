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

namespace Valkyrja\Http\Enums;

/**
 * Enum Scheme.
 *
 * @author Melech Mizrachi
 */
enum Scheme
{
    case HTTP;
    case HTTPS;

    /**
     * Get the text representation of the scheme.
     */
    public function text(): string
    {
        return match ($this) {
            self::HTTP  => 'http',
            self::HTTPS => 'https',
        };
    }
}
