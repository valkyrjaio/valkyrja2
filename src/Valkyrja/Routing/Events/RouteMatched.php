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

namespace Valkyrja\Routing\Events;

use Valkyrja\Event\Event;
use Valkyrja\Http\Request;
use Valkyrja\Routing\Route;

/**
 * Class RouteMatched.
 *
 * @author Melech Mizrachi
 */
class RouteMatched implements Event
{
    /**
     * The route.
     */
    public Route $route;

    /**
     * The request.
     */
    public Request $request;

    /**
     * RouteMatched constructor.
     *
     * @param Route   $route   The route
     * @param Request $request The request
     */
    public function __construct(Route $route, Request $request)
    {
        $this->route   = $route;
        $this->request = $request;
    }
}
