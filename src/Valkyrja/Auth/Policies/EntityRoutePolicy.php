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

namespace Valkyrja\Auth\Policies;

use Valkyrja\Auth\EntityRoutePolicy as Contract;

/**
 * Abstract Class EntityPolicy.
 *
 * @author Melech Mizrachi
 */
abstract class EntityRoutePolicy extends EntityPolicy implements Contract
{
    /**
     * The entity param number.
     */
    protected static int $entityParamNumber = 0;

    /**
     * @inheritDoc
     */
    public static function getEntityParamNumber(): int
    {
        return static::$entityParamNumber;
    }
}
