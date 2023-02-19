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

namespace Valkyrja\Console\Config;

use Valkyrja\Config\Config as Model;

/**
 * Class Cache.
 *
 * @author Melech Mizrachi
 */
class Cache extends Model
{
    /**
     * The base64 encoded serialized commands.
     */
    public string $commands;

    /**
     * The command paths.
     */
    public array $paths;

    /**
     * The named commands.
     */
    public array $namedCommands;

    /**
     * The provided commands.
     */
    public array $provided;
}
