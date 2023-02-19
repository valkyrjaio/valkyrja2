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
 * Interface Url.
 *
 * @author Melech Mizrachi
 */
interface Url
{
    /**
     * Get a route url by name.
     *
     * @param string     $name     The name of the route to get
     * @param array|null $data     [optional] The route data if dynamic
     * @param bool       $absolute [optional] Whether this url should be absolute
     */
    public function getUrl(string $name, array $data = null, bool $absolute = null): string;

    /**
     * Get a route by path.
     *
     * @param string      $path   The path
     * @param string|null $method [optional] The method type of get
     *
     * @return Route|null
     *                    The route if found or null when no static route is
     *                    found for the path and method combination specified
     */
    public function getRouteByPath(string $path, string $method = null): ?Route;

    /**
     * Determine if a uri is internal.
     *
     * @param string $uri The uri to check
     */
    public function isInternalUri(string $uri): bool;
}
