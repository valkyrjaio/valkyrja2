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

/**
 * Trait FormatterColors.
 *
 * @author Melech Mizrachi
 */
trait FormatterColors
{
    /**
     * @inheritDoc
     */
    public function black(bool $background = null): void
    {
        $this->setColor($background ? FormatBackground::BLACK : FormatForeground::BLACK, $background);
    }

    /**
     * @inheritDoc
     */
    public function red(bool $background = null): void
    {
        $this->setColor($background ? FormatBackground::RED : FormatForeground::RED, $background);
    }

    /**
     * @inheritDoc
     */
    public function green(bool $background = null): void
    {
        $this->setColor($background ? FormatBackground::GREEN : FormatForeground::GREEN, $background);
    }

    /**
     * @inheritDoc
     */
    public function yellow(bool $background = null): void
    {
        $this->setColor($background ? FormatBackground::YELLOW : FormatForeground::YELLOW, $background);
    }

    /**
     * @inheritDoc
     */
    public function blue(bool $background = null): void
    {
        $this->setColor($background ? FormatBackground::BLUE : FormatForeground::BLUE, $background);
    }

    /**
     * @inheritDoc
     */
    public function magenta(bool $background = null): void
    {
        $this->setColor($background ? FormatBackground::MAGENTA : FormatForeground::MAGENTA, $background);
    }

    /**
     * @inheritDoc
     */
    public function cyan(bool $background = null): void
    {
        $this->setColor($background ? FormatBackground::CYAN : FormatForeground::CYAN, $background);
    }

    /**
     * @inheritDoc
     */
    public function white(bool $background = null): void
    {
        $this->setColor($background ? FormatBackground::WHITE : FormatForeground::WHITE, $background);
    }

    /**
     * Set a color.
     *
     * @param FormatBackground $color      The color
     * @param bool             $background [optional] Whether this is to set the background
     */
    abstract protected function setColor(FormatBackground $color, bool $background = null): void;
}
