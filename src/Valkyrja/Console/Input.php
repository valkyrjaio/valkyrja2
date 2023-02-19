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

use Valkyrja\Console\Inputs\Option;

/**
 * Interface Input.
 *
 * @author Melech Mizrachi
 */
interface Input
{
    /**
     * Get the arguments.
     */
    public function getArguments(): array;

    /**
     * Get the short options.
     */
    public function getShortOptions(): array;

    /**
     * Get the arguments.
     */
    public function getLongOptions(): array;

    /**
     * Get the arguments.
     */
    public function getOptions(): array;

    /**
     * Get the arguments as a string.
     */
    public function getStringArguments(): string;

    /**
     * Get the input arguments.
     */
    public function getInputArguments(): array;

    /**
     * Get an argument.
     *
     * @param string $argument The argument
     */
    public function getArgument(string $argument): ?string;

    /**
     * Determine if an argument exists.
     *
     * @param string $argument The argument
     */
    public function hasArgument(string $argument): bool;

    /**
     * Get a short option.
     *
     * @param string $option The option
     */
    public function getShortOption(string $option): ?string;

    /**
     * Determine if a short option exists.
     *
     * @param string $option The short option
     */
    public function hasShortOption(string $option): bool;

    /**
     * Get a long option.
     *
     * @param string $option The option
     */
    public function getLongOption(string $option): ?string;

    /**
     * Determine if a long option exists.
     *
     * @param string $option The option
     */
    public function hasLongOption(string $option): bool;

    /**
     * Get an option (short or long).
     *
     * @param string $option The option
     */
    public function getOption(string $option): ?string;

    /**
     * Check if an option exists (long or short).
     *
     * @param string $option The option
     */
    public function hasOption(string $option): bool;

    /**
     * Get the global options.
     *
     * @return Option[]
     */
    public function getGlobalOptions(): array;

    /**
     * Get the global options as a flat array.
     *
     * @return string[]
     */
    public function getGlobalOptionsFlat(): array;
}
