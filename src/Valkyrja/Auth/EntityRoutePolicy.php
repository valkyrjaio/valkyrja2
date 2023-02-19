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

namespace Valkyrja\Auth;

/**
 * Interface EntityRoutePolicy.
 *
 * @author Melech Mizrachi
 */
interface EntityRoutePolicy extends EntityPolicy
{
    /**
     * Get the entity param number indexed at 0.
     *  For example if the route was defined as `get('/path/{entity1}/entity2')` with an action of
     *  `action(Entity1 $entity1, Entity2 $entity2)` you would set 0 to get the first entity or
     *  1 for the second, etc.
     */
    public static function getEntityParamNumber(): int;
}
