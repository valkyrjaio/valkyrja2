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

namespace Valkyrja\Routing\Facades;

use Valkyrja\Application\Applications\Valkyrja;
use Valkyrja\Facade\Facades\Facade;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Routing\Collection;
use Valkyrja\Routing\Matcher;
use Valkyrja\Routing\Route;

/**
 * Class Router.
 *
 * @author Melech Mizrachi
 *
 * @method static Route get(string $path, $handler, string $name = null)
 * @method static Route post(string $path, $handler, string $name = null)
 * @method static Route put(string $path, $handler, string $name = null)
 * @method static Route patch(string $path, $handler, string $name = null)
 * @method static Route delete(string $path, $handler, string $name = null)
 * @method static Route head(string $path, $handler, string $name = null)
 * @method static Route any(string $path, $handler, string $name = null)
 * @method static Route redirect(string $path, string $to, array $methods = null, string $name = null)
 * @method static Collection collection()
 * @method static Matcher matcher()
 * @method static void addRoute(Route $route)
 * @method static Route[] getRoutes()
 * @method static Route getRoute(string $name)
 * @method static bool hasRoute(string $name)
 * @method static string getUrl(string $name, array $data = null, bool $absolute = null)
 * @method static Route getRouteFromRequest(Request $request)
 * @method static Route|null getRouteByPath(string $path, string $method = null)
 * @method static bool isInternalUri(string $uri)
 * @method static Response dispatch(Request $request)
 */
class Router extends Facade
{
    /**
     * The facade instance.
     *
     * @return string|object
     */
    public static function instance()
    {
        return Valkyrja::app()->router();
    }
}
