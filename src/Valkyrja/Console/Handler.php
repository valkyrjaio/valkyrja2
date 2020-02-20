<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Console;

/**
 * Interface Handler.
 *
 * @author Melech Mizrachi
 */
interface Handler
{
    /**
     * The command.
     */
    public const COMMAND           = '';
    public const PATH              = '';
    public const SHORT_DESCRIPTION = '';
    public const DESCRIPTION       = '';

    /**
     * Help docs for this command.
     *
     * @return int
     */
    public function help(): int;

    /**
     * Get the command version.
     *
     * @return int
     */
    public function version(): int;
}
