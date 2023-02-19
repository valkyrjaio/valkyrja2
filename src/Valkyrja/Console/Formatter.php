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

use Valkyrja\Console\Enums\FormatBackground;
use Valkyrja\Console\Enums\FormatForeground;
use Valkyrja\Console\Enums\FormatOption;

/**
 * Interface Formatter.
 *
 * @author Melech Mizrachi
 */
interface Formatter
{
    /**
     * Set the foreground.
     *
     * @param FormatForeground|null $foreground The foreground color
     */
    public function setForeground(FormatForeground $foreground = null): void;

    /**
     * Set the background.
     *
     * @param FormatBackground|null $background The background
     */
    public function setBackground(FormatBackground $background = null): void;

    /**
     * Set foreground or background to black.
     *
     * @param bool $background [optional] Whether this is to set the background
     */
    public function black(bool $background = null): void;

    /**
     * Set foreground or background to red.
     *
     * @param bool $background [optional] Whether this is to set the background
     */
    public function red(bool $background = null): void;

    /**
     * Set foreground or background to green.
     *
     * @param bool $background [optional] Whether this is to set the background
     */
    public function green(bool $background = null): void;

    /**
     * Set foreground or background to yellow.
     *
     * @param bool $background [optional] Whether this is to set the background
     */
    public function yellow(bool $background = null): void;

    /**
     * Set foreground or background to blue.
     *
     * @param bool $background [optional] Whether this is to set the background
     */
    public function blue(bool $background = null): void;

    /**
     * Set foreground or background to magenta.
     *
     * @param bool $background [optional] Whether this is to set the background
     */
    public function magenta(bool $background = null): void;

    /**
     * Set foreground or background to cyan.
     *
     * @param bool $background [optional] Whether this is to set the background
     */
    public function cyan(bool $background = null): void;

    /**
     * Set foreground or background to white.
     *
     * @param bool $background [optional] Whether this is to set the background
     */
    public function white(bool $background = null): void;

    /**
     * Set foreground or background to default.
     *
     * @param bool $background [optional] Whether this is to set the background
     */
    public function resetColor(bool $background = null): void;

    /**
     * Set an option.
     *
     * @param FormatOption $option The option
     */
    public function setOption(FormatOption $option): void;

    /**
     * Determine whether an option has been set.
     *
     * @param FormatOption $option The option
     */
    public function hasOption(FormatOption $option): bool;

    /**
     * Remove an option.
     *
     * @param FormatOption $option The option
     */
    public function removeOption(FormatOption $option): void;

    /**
     * Set options.
     *
     * @param FormatOption ...$options The options
     */
    public function setOptions(FormatOption ...$options): void;

    /**
     * Set the bold option.
     */
    public function bold(): void;

    /**
     * Set the underscore option.
     */
    public function underscore(): void;

    /**
     * Set the blink option.
     */
    public function blink(): void;

    /**
     * Set the reverse option.
     */
    public function reverse(): void;

    /**
     * Set the conceal option.
     */
    public function conceal(): void;

    /**
     * Reset the options.
     */
    public function resetOptions(): void;

    /**
     * Format a message.
     *
     * @param string $message The message
     */
    public function format(string $message): string;
}
