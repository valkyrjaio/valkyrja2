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

namespace Valkyrja\Routing\Collectors;

use Closure;
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Routing\Collection;
use Valkyrja\Routing\Collector as Contract;
use Valkyrja\Routing\Models\Route as RouteModel;
use Valkyrja\Routing\Route;

/**
 * Class Collector.
 *
 * @author Melech Mizrachi
 */
class Collector implements Contract
{
    use CollectorHelpers;

    /**
     * Collector constructor.
     *
     * @param Collection $collection The collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @inheritDoc
     */
    public function withPath(string $path): static
    {
        return $this->withGroupableSelf('setPath', $path);
    }

    /**
     * @inheritDoc
     */
    public function withController(string $controller): static
    {
        return $this->withGroupableSelf('setClass', $controller);
    }

    /**
     * @inheritDoc
     */
    public function withName(string $name): static
    {
        return $this->withGroupableSelf('setName', $name);
    }

    /**
     * @inheritDoc
     */
    public function withMiddleware(array $middleware): static
    {
        return $this->withGroupableSelf('setMiddleware', $middleware);
    }

    /**
     * @inheritDoc
     */
    public function withSecure(bool $secure = true): static
    {
        return $this->withGroupableSelf('setSecure', $secure);
    }

    /**
     * @inheritDoc
     */
    public function group(Closure $group): static
    {
        $group($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $path, Closure|string $handler, string $name = null, bool $setDependencies = true): Route
    {
        return $this->setMethodsAndAddRoute(
            [
                RequestMethod::GET,
                RequestMethod::HEAD,
            ],
            $path,
            $handler,
            $name,
            $setDependencies
        );
    }

    /**
     * @inheritDoc
     */
    public function post(string $path, Closure|string $handler, string $name = null, bool $setDependencies = true): Route
    {
        return $this->setMethodsAndAddRoute([RequestMethod::POST], $path, $handler, $name, $setDependencies);
    }

    /**
     * @inheritDoc
     */
    public function put(string $path, Closure|string $handler, string $name = null, bool $setDependencies = true): Route
    {
        return $this->setMethodsAndAddRoute([RequestMethod::PUT], $path, $handler, $name, $setDependencies);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $path, Closure|string $handler, string $name = null, bool $setDependencies = true): Route
    {
        return $this->setMethodsAndAddRoute([RequestMethod::PATCH], $path, $handler, $name, $setDependencies);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, Closure|string $handler, string $name = null, bool $setDependencies = true): Route
    {
        return $this->setMethodsAndAddRoute([RequestMethod::DELETE], $path, $handler, $name, $setDependencies);
    }

    /**
     * @inheritDoc
     */
    public function head(string $path, Closure|string $handler, string $name = null, bool $setDependencies = true): Route
    {
        return $this->setMethodsAndAddRoute([RequestMethod::HEAD], $path, $handler, $name, $setDependencies);
    }

    /**
     * @inheritDoc
     */
    public function any(string $path, Closure|string $handler, string $name = null, bool $setDependencies = true): Route
    {
        return $this->setMethodsAndAddRoute(RequestMethod::ANY, $path, $handler, $name, $setDependencies);
    }

    /**
     * @inheritDoc
     */
    public function redirect(string $path, string $to, array $methods = null, string $name = null): Route
    {
        return (new RouteModel())
            ->setPath($path)
            ->setTo($to)
            ->setName($name)
            ->setMethods($methods ?? [RequestMethod::GET, RequestMethod::HEAD]);
    }
}
