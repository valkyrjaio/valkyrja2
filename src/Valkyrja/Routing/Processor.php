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

namespace Valkyrja\Routing;

/**
 * Interface Processor.
 *
 * @author Melech Mizrachi
 */
interface Processor
{
    /**
     * Process a route.
     *
     * @param Route $route The route
     */
    public function route(Route $route): void;
}
