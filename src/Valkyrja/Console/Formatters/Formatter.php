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

namespace Valkyrja\Console\Formatters;

use Valkyrja\Console\Enums\FormatBackground;
use Valkyrja\Console\Enums\FormatForeground;
use Valkyrja\Console\Enums\FormatOption;
use Valkyrja\Console\Formatter as Contract;

use function count;
use function implode;
use function sprintf;

/**
 * Class OutputFormatter.
 *
 * @author Melech Mizrachi
 */
class Formatter implements Contract
{
    use FormatterColors;
    use FormatterOptions;

    /**
     * The foreground color.
     */
    protected ?int $foreground = null;

    /**
     * The background color.
     */
    protected ?int $background = null;

    /**
     * The options.
     *
     * @var int[]
     */
    protected array $options = [];

    /**
     * @inheritDoc
     */
    public function setForeground(FormatForeground $foreground = null): void
    {
        $this->foreground = $foreground?->value;
    }

    /**
     * @inheritDoc
     */
    public function setBackground(FormatBackground $background = null): void
    {
        $this->background = $background?->value;
    }

    /**
     * @inheritDoc
     */
    public function resetColor(bool $background = null): void
    {
        $this->setColor($background ? FormatBackground::DEFAULT : FormatForeground::DEFAULT, $background);
    }

    /**
     * @inheritDoc
     */
    public function setOption(FormatOption $option): void
    {
        $this->options[$option->getValue()] = $option->getValue();
    }

    /**
     * @inheritDoc
     */
    public function hasOption(FormatOption $option): bool
    {
        return isset($this->options[$option->getValue()]);
    }

    /**
     * @inheritDoc
     */
    public function removeOption(FormatOption $option): void
    {
        if ($this->hasOption($option)) {
            unset($this->options[$option->getValue()]);
        }
    }

    /**
     * @inheritDoc
     */
    public function setOptions(FormatOption ...$options): void
    {
        foreach ($options as $option) {
            $this->setOption($option);
        }
    }

    /**
     * @inheritDoc
     */
    public function resetOptions(): void
    {
        $this->options = [];
    }

    /**
     * @inheritDoc
     */
    public function format(string $message): string
    {
        $set   = [];
        $unset = [];

        // Check if a foreground was specified
        if ($this->foreground !== null) {
            $set[]   = $this->foreground;
            $unset[] = FormatForeground::DEFAULT->value;
        }

        // Check if a background was specified
        if ($this->background !== null) {
            $set[]   = $this->background;
            $unset[] = FormatBackground::DEFAULT->value;
        }

        // Check if options were specified
        if (count($this->options)) {
            // Iterate through all the options
            foreach ($this->options as $option) {
                $set[]   = $option;
                $unset[] = FormatOption::DEFAULT[$option];
            }
        }

        // No need to format if there's nothing to set
        if (count($set) === 0) {
            return $message;
        }

        return sprintf("\033[%sm%s\033[%sm", implode(';', $set), $message, implode(';', $unset));
    }

    /**
     * Set a color.
     *
     * @param FormatBackground|FormatForeground $color      The color
     * @param bool                              $background [optional] Whether this is to set the background
     */
    protected function setColor(FormatBackground|FormatForeground $color, bool $background = null): void
    {
        if ($background !== null) {
            $this->background = $color->value;

            return;
        }

        $this->foreground = $color->value;
    }

    /**
     * Set an option by its number value.
     *
     * @param int $option The option
     */
    protected function setOptionNum(int $option): void
    {
        $this->options[$option] = $option;
    }
}
