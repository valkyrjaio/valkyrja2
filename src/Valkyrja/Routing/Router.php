<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Routing;

use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Support\Cacheable;

/**
 * Interface Router.
 *
 * @author Melech Mizrachi
 */
interface Router extends Cacheable, MethodHelpers, RouteHelpers
{
    /**
     * Get the route collection.
     *
     * @return Collection
     */
    public function collection(): Collection;

    /**
     * Get the route matcher.
     *
     * @return Matcher
     */
    public function matcher(): Matcher;

    /**
     * Dispatch the route and find a match.
     *
     * @param Request $request The request
     *
     * @return Response
     */
    public function dispatch(Request $request): Response;
}
