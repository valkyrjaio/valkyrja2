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

namespace Valkyrja\Routing\Urls;

use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Http\Request;
use Valkyrja\Routing\Config\Config;
use Valkyrja\Routing\Exceptions\InvalidRouteName;
use Valkyrja\Routing\Route;
use Valkyrja\Routing\Router;
use Valkyrja\Routing\Url as Contract;

use function str_replace;
use function strlen;
use function strpos;
use function substr;

/**
 * Class Url.
 *
 * @author Melech Mizrachi
 */
class Url implements Contract
{
    /**
     * Router constructor.
     *
     * @param Request      $request The request
     * @param Router       $router  The router
     * @param Config|array $config  The routing config
     */
    public function __construct(
        protected Request $request,
        protected Router $router,
        protected Config|array $config
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidRouteName
     */
    public function getUrl(string $name, array $data = null, bool $absolute = null): string
    {
        // Get the matching route
        $route = $this->router->getRoute($name);
        // Set the host to use if this is an absolute url
        // or the config is set to always use absolute urls
        // or the route is secure (needs https:// appended)
        $host = $absolute || $route->isSecure() || $this->config['useAbsoluteUrls']
            ? $this->routeHost($route)
            : '';
        // Get the path from the generator
        $path = $route->getPath();

        // If any data was passed
        if ($data) {
            // Iterate through the data and replace it in the path
            foreach ($data as $datumName => $datum) {
                $path = str_replace('{' . $datumName . '}', $datum, $path);
            }
        }

        return $host . $this->validateRouteUrl($path);
    }

    /**
     * @inheritDoc
     */
    public function getRouteByPath(string $path, string $method = null): ?Route
    {
        return $this->router->getMatcher()->match($path, $method ?? RequestMethod::GET);
    }

    /**
     * @inheritDoc
     */
    public function isInternalUri(string $uri): bool
    {
        // Replace the scheme if it exists
        $uri = str_replace(['http://', 'https://'], '', $uri);

        // Get the host of the uri
        $host = substr($uri, 0, (int) strpos($uri, '/'));

        // If the host does not match the current request uri's host
        if ($host && $host !== $this->request->getUri()->getHost()) {
            // Return false immediately
            return false;
        }

        // Get only the path (full string from the first slash to the end of the path)
        $uri = substr($uri, (int) strpos($uri, '/'), strlen($uri));

        // Try to match the route
        $route = $this->getRouteByPath($uri);

        return $route !== null;
    }

    /**
     * Get a route's host.
     *
     * @param Route $route The route
     */
    protected function routeHost(Route $route): string
    {
        return 'http'
            . ($route->isSecure() ? 's' : '')
            . '://'
            . $this->request->getUri()->getHostPort();
    }

    /**
     * Validate the route url.
     *
     * @param string $path The path
     */
    protected function validateRouteUrl(string $path): string
    {
        // If the last character is not a slash and the config is set to
        // ensure trailing slash
        if ($path[-1] !== '/' && $this->config['useTrailingSlash']) {
            // add a trailing slash
            $path .= '/';
        }

        return $path;
    }
}
