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

use Valkyrja\Console\Enums\ArgumentMode;

/**
 * Class Argument.
 *
 * @author Melech Mizrachi
 */
class Argument
{
    /**
     * The name.
     */
    protected string $name;

    protected string $description;

    /**
     * The mode.
     */
    protected ArgumentMode $mode;

    /**
     * Argument constructor.
     *
     * @param string            $name        The name
     * @param string            $description The description
     * @param ArgumentMode|null $mode        [optional] The mode
     */
    public function __construct(string $name, string $description, ArgumentMode $mode = null)
    {
        $this->name        = $name;
        $this->description = $description;
        $this->mode        = $mode ?? ArgumentMode::OPTIONAL;
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the mode.
     */
    public function getMode(): ArgumentMode
    {
        return $this->mode;
    }
}
