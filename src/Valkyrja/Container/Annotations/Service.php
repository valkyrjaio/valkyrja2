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

namespace Valkyrja\Container\Annotations;

use Valkyrja\Annotation\Annotation;
use Valkyrja\Annotation\Models\Annotatable;
use Valkyrja\Dispatcher\Models\Dispatch;

/**
 * Class Service.
 *
 * @author Melech Mizrachi
 */
class Service extends Dispatch implements Annotation
{
    use Annotatable;

    /**
     * Whether this service is a singleton.
     */
    public bool $singleton = false;

    /**
     * Default arguments.
     */
    public ?array $defaults;

    /**
     * Get whether this is a singleton.
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * Set whether this is a singleton.
     *
     * @param bool $singleton Whether this is a singleton
     */
    public function setSingleton(bool $singleton = true): static
    {
        $this->singleton = $singleton;

        return $this;
    }

    /**
     * Get defaults.
     */
    public function getDefaults(): ?array
    {
        return $this->defaults ?? null;
    }

    /**
     * Set defaults.
     *
     * @param array|null $defaults the defaults
     */
    public function setDefaults(array $defaults = null): static
    {
        $this->defaults = $defaults;

        return $this;
    }
}
