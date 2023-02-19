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

namespace Valkyrja\Routing\Attributes;

use InvalidArgumentException;
use ReflectionException;
use Valkyrja\Attribute\Attributes as AttributeAttributes;
use Valkyrja\Reflection\Reflector;
use Valkyrja\Routing\Attributes as Contract;
use Valkyrja\Routing\Exceptions\InvalidRoutePath;
use Valkyrja\Routing\Processor;

/**
 * Class Attributes.
 *
 * @author Melech Mizrachi
 */
class Attributes implements Contract
{
    public function __construct(
        protected AttributeAttributes $attributes,
        protected Reflector $reflector,
        protected Processor $processor
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidRoutePath
     * @throws ReflectionException
     */
    public function getRoutes(string ...$classes): array
    {
        $routes = [];

        foreach ($classes as $class) {
            /** @var Route[] $classAttributes */
            $classAttributes = $this->attributes->forClass($class, Route::class);
            /** @var Route[] $memberAttributes */
            $memberAttributes = $this->attributes->forClassMembers($class, Route::class);

            $finalAttributes = [];

            // If this class has attributes
            if (! empty($classAttributes)) {
                // Iterate through all the class attributes
                foreach ($classAttributes as $classAttribute) {
                    /** @var Parameter[] $mergedClassParameters */
                    $mergedClassParameters = [
                        ...$classAttribute->getParameters(),
                        ...$this->attributes->forClass($class, Parameter::class),
                    ];
                    $mergedClassMiddleware = [
                        ...($classAttribute->getMiddleware() ?? []),
                        ...array_column($this->attributes->forClass($class, Middleware::class), 'name'),
                    ];

                    $classAttribute->setParameters($mergedClassParameters);
                    $classAttribute->setMiddleware($mergedClassMiddleware);

                    // If the class' members' had attributes
                    if (! empty($memberAttributes)) {
                        // Iterate through all the members' attributes
                        foreach ($memberAttributes as $routeAttribute) {
                            $routeParameters = [];
                            $routeMiddleware = [];

                            if ($property = $routeAttribute->getProperty()) {
                                $routeParameters = $this->attributes->forProperty($class, $property, Parameter::class);
                                $routeMiddleware = $this->attributes->forProperty($class, $property, Middleware::class);
                            } elseif ($method = $routeAttribute->getMethod()) {
                                $routeParameters = $this->attributes->forMethod($class, $method, Parameter::class);
                                $routeMiddleware = $this->attributes->forMethod($class, $method, Middleware::class);
                            }

                            /** @var Parameter[] $mergedPropertyParameters */
                            $mergedPropertyParameters = [
                                ...$routeAttribute->getParameters(),
                                ...$routeParameters,
                            ];
                            $mergedPropertyMiddleware = [
                                ...($routeAttribute->getMiddleware() ?? []),
                                ...array_column($routeMiddleware, 'name'),
                            ];

                            $routeAttribute->setParameters($mergedPropertyParameters);
                            $routeAttribute->setMiddleware($mergedPropertyMiddleware);

                            // And set a new route with the controller defined annotation additions
                            $finalAttributes[] = $this->getControllerBuiltRoute($classAttribute, $routeAttribute);
                        }
                    }

                    // Figure out if there should be an else that automatically sets routes from the class attributes
                }
            } else {
                $finalAttributes = $memberAttributes;
            }

            $routes = [
                ...$routes,
                ...$finalAttributes,
            ];
        }

        foreach ($routes as $route) {
            $this->setRouteProperties($route);
        }

        return $routes;
    }

    /**
     * Set the route properties from arguments.
     *
     * @throws InvalidRoutePath
     * @throws ReflectionException
     */
    protected function setRouteProperties(Route $route): void
    {
        if (($class = $route->getClass()) === null) {
            throw new InvalidArgumentException('Invalid class defined in route.');
        }

        if (($method = $route->getMethod()) !== null) {
            $methodReflection = $this->reflector->getMethodReflection($class, $method);
            // Set the dependencies
            $route->setDependencies($this->reflector->getDependencies($methodReflection));
        }

        // Avoid having large arrays in cached routes file
        $route->setMatches();

        $this->processor->route($route);
    }

    /**
     * Get a new attribute with controller attribute additions.
     *
     * @param Route $controllerAttribute The controller route attribute
     * @param Route $memberAttribute     The member route attribute
     */
    protected function getControllerBuiltRoute(Route $controllerAttribute, Route $memberAttribute): Route
    {
        $attribute = clone $memberAttribute;

        if (! $memberAttribute->getPath()) {
            throw new InvalidArgumentException('Invalid path defined in route.');
        }

        // Get the route's path
        $path           = $this->getFilteredPath($memberAttribute->getPath());
        $controllerPath = $this->getFilteredPath($controllerAttribute->getPath());

        // Set the path to the base path and route path
        $attribute->setPath($this->getFilteredPath($controllerPath . $path));

        // If there is a base name for this controller
        if (($controllerName = $controllerAttribute->getName()) !== null) {
            // Set the name to the base name and route name
            $attribute->setName($controllerName . (($name = $memberAttribute->getName()) ? '.' . $name : ''));
        }

        // If the base is dynamic
        if ($controllerAttribute->isDynamic()) {
            // Set the route to dynamic
            $attribute->setDynamic();
        }

        // If the base is secure
        if ($controllerAttribute->isSecure()) {
            // Set the route to dynamic
            $attribute->setSecure();
        }

        // If there is a base middleware collection for this controller
        if (($controllerMiddleware = $controllerAttribute->getMiddleware()) !== null) {
            // Merge the route's middleware and the controller's middleware
            // keeping the controller's middleware first
            $attribute->setMiddleware(
                [
                    ...$controllerMiddleware,
                    ...($memberAttribute->getMiddleware() ?? []),
                ]
            );
        }

        // If there is a base parameters collection for this controller
        if (! empty($controllerParameters = $controllerAttribute->getParameters())) {
            // Merge the route's parameters and the controller's parameters
            // keeping the controller's parameters first
            $attribute->setMiddleware(
                [
                    ...$controllerParameters,
                    ...$memberAttribute->getParameters(),
                ]
            );
        }

        return $attribute;
    }

    /**
     * Validate a path.
     *
     * @param string $path The path
     */
    protected function getFilteredPath(string $path): string
    {
        // Trim slashes from the beginning and end of the path
        if (! $path = trim($path, '/')) {
            // If the path only had a slash return as just slash
            return '/';
        }

        // If the route doesn't begin with an optional or required group
        if ($path[0] !== '[' && $path[0] !== '<') {
            // Append a slash
            return '/' . $path;
        }

        return $path;
    }
}
