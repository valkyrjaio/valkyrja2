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

namespace Valkyrja\Container\Annotation\Models;

use Valkyrja\Annotation\Models\Annotatable;
use Valkyrja\Container\Annotation\Service as Contract;
use Valkyrja\Dispatcher\Models\Dispatch;

/**
 * Class Service.
 *
 * @author Melech Mizrachi
 */
class Service extends Dispatch implements Contract
{
    use Annotatable;

    /**
     * Whether this service is a singleton.
     *
     * @var bool
     */
    public bool $singleton = false;

    /**
     * Default arguments.
     *
     * @var array|null
     */
    public ?array $defaults;

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
        return $this->defaults ?? null;
    }

    /**
     * Set defaults.
     *
     * @param array|null $defaults The defaults.
     *
     * @return static
     */
    public function setDefaults(array $defaults = null): self
    {
        $this->defaults = $defaults;

        return $this;
    }
}
