<?php

declare(strict_types = 1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Container\Models;

/**
 * Trait Serviceable.
 *
 * @author Melech Mizrachi
 */
trait Serviceable
{
    /**
     * Whether this service is a singleton.
     *
     * @var bool
     */
    protected bool $singleton = false;

    /**
     * Default arguments.
     *
     * @var array|null
     */
    protected ?array $defaults = null;

    /**
     * Get whether this is a singleton.
     *
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * Set whether this is a singleton.
     *
     * @param bool $singleton Whether this is a singleton
     *
     * @return static
     */
    public function setSingleton(bool $singleton = true): self
    {
        $this->singleton = $singleton;

        return $this;
    }

    /**
     * Get defaults.
     *
     * @return array|null
     */
    public function getDefaults(): ?array
    {
        return $this->defaults;
    }

    /**
     * Set defaults.
     *
     * @param array $defaults The defaults.
     *
     * @return static
     */
    public function setDefaults(array $defaults = null): self
    {
        $this->defaults = $defaults;

        return $this;
    }
}
