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

namespace Valkyrja\Routing\Annotators;

use InvalidArgumentException;
use ReflectionException;
use Valkyrja\Annotation\Annotator as AnnotationAnnotator;
use Valkyrja\Annotation\Filter;
use Valkyrja\Reflection\Reflector;
use Valkyrja\Routing\Annotations\Route;
use Valkyrja\Routing\Annotator as Contract;
use Valkyrja\Routing\Enums\AnnotationName;
use Valkyrja\Routing\Exceptions\InvalidRoutePath;
use Valkyrja\Routing\Models\Route as RouteModel;
use Valkyrja\Routing\Processor;
use Valkyrja\Routing\Route as RouteContract;

use function array_merge;
use function trim;

/**
 * Class RouteAnnotator.
 *
 * @author Melech Mizrachi
 */
class Annotator implements Contract
{
    /**
     * ContainerAnnotator constructor.
     */
    public function __construct(
        protected AnnotationAnnotator $annotator,
        protected Filter $filter,
        protected Reflector $reflector,
        protected Processor $processor
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     * @throws InvalidRoutePath
     * @throws InvalidArgumentException
     */
    public function getRoutes(string ...$classes): array
    {
        $routes = $this->getClassRoutes($classes);
        /** @var RouteContract[] $finalRoutes */
        $finalRoutes = [];

        // Iterate through all the routes
        foreach ($routes as $route) {
            $class = $route->getClass();
            $path  = $route->getPath();

            if ($class === null) {
                throw new InvalidArgumentException('Invalid class or path defined in route.');
            }

            // Set the route's properties
            $this->setRouteProperties($route);

            // Get the class's annotations
            $classAnnotations = $this->getClassAnnotations($class);

            // If this route's class has annotations
            if (! empty($classAnnotations)) {
                /** @var Route $annotation */
                // Iterate through all the annotations
                foreach ($classAnnotations as $annotation) {
                    // And set a new route with the controller defined annotation additions
                    $finalRoutes[] = $this->getRouteFromAnnotation($this->getControllerBuiltRoute($annotation, $route));
                }
            } else {
                // Validate the path before setting the route
                $route->setPath($this->getParsedPath($path));

                // Otherwise just set the route in the final array
                $finalRoutes[] = $this->getRouteFromAnnotation($route);
            }
        }

        return $finalRoutes;
    }

    /**
     * Get all classes' routes.
     *
     * @param class-string[] $classes The classes
     *
     * @return Route[]
     */
    protected function getClassRoutes(array $classes): array
    {
        $routes = [];

        // Iterate through all the classes
        foreach ($classes as $class) {
            // Set the annotations in the routes list
            $routes[] = $this->getClassMemberAnnotations($class);
        }

        /** @var array<int, Route[]> $routes */
        return array_merge(...$routes);
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

        if ($route->getProperty() === null) {
            $method = $route->getMethod() ?? '__construct';

            $methodReflection = $this->reflector->getMethodReflection($class, $method);

            // Set the dependencies
            $route->setDependencies($this->reflector->getDependencies($methodReflection));
        }

        // Avoid having large arrays in cached routes file
        $route->setMatches();
    }

    /**
     * Get class annotations.
     *
     * @param class-string $class The class
     */
    protected function getClassAnnotations(string $class): array
    {
        return $this->filter->filterAnnotationsByTypes(
            AnnotationName::getValidValues(),
            ...$this->annotator->classAnnotations($class)
        );
    }

    /**
     * Get a route from a route annotation.
     *
     * @param Route $route The route annotation
     *
     * @throws InvalidArgumentException
     */
    protected function getRouteFromAnnotation(Route $route): RouteContract
    {
        $finalRoute = RouteModel::fromArray($route->asArray());

        $this->processor->route($finalRoute);

        return $finalRoute;
    }

    /**
     * Get a new route with controller route additions.
     */
    protected function getControllerBuiltRoute(Route $controllerRoute, Route $route): Route
    {
        $newRoute = clone $route;

        $path = $route->getPath();

        // Get the route's path
        $path           = $this->getParsedPath($path);
        $controllerPath = $this->getParsedPath($controllerRoute->getPath());

        // Set the path to the base path and route path
        $newRoute->setPath($this->getParsedPath($controllerPath . $path));

        // If there is a base name for this controller
        if (($controllerName = $controllerRoute->getName()) !== null) {
            // Set the name to the base name and route name
            $newRoute->setName($controllerName . (($name = $route->getName()) ? '.' . $name : ''));
        }

        // If the base is dynamic
        if ($controllerRoute->isDynamic()) {
            // Set the route to dynamic
            $newRoute->setDynamic();
        }

        // If the base is secure
        if ($controllerRoute->isSecure()) {
            // Set the route to dynamic
            $newRoute->setSecure();
        }

        // If there is a base middleware collection for this controller
        if (($controllerMiddleware = $controllerRoute->getMiddleware()) !== null) {
            // Merge the route's middleware and the controller's middleware
            // keeping the controller's middleware first
            $middleware = array_merge($controllerMiddleware, $route->getMiddleware() ?? []);

            // Set the middleware in the route
            $newRoute->setMiddleware($middleware);
        }

        return $newRoute;
    }

    /**
     * Get a parsed path.
     *
     * @param string $path The path
     */
    protected function getParsedPath(string $path): string
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

    /**
     * Get class member annotations.
     *
     * @param class-string $class The class
     */
    protected function getClassMemberAnnotations(string $class): array
    {
        return $this->filter->filterAnnotationsByTypes(
            AnnotationName::getValidValues(),
            ...$this->annotator->classMembersAnnotations($class)
        );
    }
}
