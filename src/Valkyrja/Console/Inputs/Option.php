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

namespace Valkyrja\Console\Inputs;

use Valkyrja\Console\Enums\OptionMode;

/**
 * Class Option.
 *
 * @author Melech Mizrachi
 */
class Option
{
    /**
     * The name.
     */
    protected string $name;

    /**
     * The shortcut.
     */
    protected ?string $shortcut = null;

    /**
     * The description.
     */
    protected string $description;

    /**
     * The default value.
     */
    protected ?string $default = null;

    /**
     * The mode.
     */
    protected OptionMode $mode;

    /**
     * Option constructor.
     *
     * @param string          $name        The name
     * @param string          $description The description
     * @param string|null     $shortcut    [optional] The shortcut
     * @param OptionMode|null $mode        [optional] The mode
     * @param string|null     $default     [optional] The default value
     */
    public function __construct(
        string $name,
        string $description,
        string $shortcut = null,
        OptionMode $mode = null,
        string $default = null
    ) {
        $this->name        = $name;
        $this->shortcut    = $shortcut;
        $this->description = $description;
        $this->mode        = $mode ?? OptionMode::NONE;
        $this->default     = $default;
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the shortcut.
     */
    public function getShortcut(): ?string
    {
        return $this->shortcut;
    }

    /**
     * Get the description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * The default value.
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * Get the mode.
     */
    public function getMode(): OptionMode
    {
        return $this->mode;
    }
}
