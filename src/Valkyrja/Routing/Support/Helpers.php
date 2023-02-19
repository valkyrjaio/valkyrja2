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

namespace Valkyrja\Routing\Support;

/**
 * Class Helpers.
 *
 * @author Melech Mizrachi
 */
class Helpers
{
    /**
     * Trim a path.
     *
     * @param string $path The path
     */
    public static function trimPath(string $path): string
    {
        return '/' . trim($path, '/');
    }
}
