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

namespace Valkyrja\Tests\Classes\Orm;

use Valkyrja\Orm\Entities\Entity as AbstractEntity;

/**
 * Model class to use to test abstract model.
 *
 * @author Melech Mizrachi
 */
class Entity extends AbstractEntity
{
    /**
     * A property to test with.
     */
    public ?string $property = null;

    /**
     * A property to test with using getter/setter.
     */
    protected ?string $prop = null;

    /**
     * Get the prop.
     *
     * @return string
     */
    public function getProp(): ?string
    {
        return $this->prop;
    }

    /**
     * Determine if the prop is set.
     */
    public function issetProp(): bool
    {
        return $this->prop !== null;
    }

    /**
     * Set the prop.
     *
     * @param string $prop The prop
     */
    public function setProp(string $prop): Entity
    {
        $this->prop = $prop;

        return $this;
    }
}
