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

namespace Valkyrja\Console;

/**
 * Interface Commander.
 *
 * @author Melech Mizrachi
 */
interface Commander
{
    /**
     * Get the command.
     */
    public static function getCommand(): string;

    /**
     * Get the command path.
     */
    public static function getPath(): string;

    /**
     * Get the short description.
     */
    public static function getShortDescription(): string;

    /**
     * Get the description.
     */
    public static function getDescription(): string;

    /**
     * Help docs for this command.
     */
    public function help(): int;

    /**
     * The run handler.
     */
    public function run(): int;

    /**
     * Get the command version.
     */
    public function version(): int;
}
