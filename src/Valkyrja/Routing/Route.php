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

use BackedEnum;
use Valkyrja\Dispatcher\Dispatch;
use Valkyrja\Orm\Entity;
use Valkyrja\Routing\Enums\CastType;
use Valkyrja\Routing\Models\Parameter;

/**
 * Interface Route.
 *
 * @author Melech Mizrachi
 */
interface Route extends Dispatch
{
    /**
     * Get the route's path.
     */
    public function getPath(): string;

    /**
     * Set the route's path.
     *
     * @param string $path The route path
     */
    public function setPath(string $path): static;

    /**
     * Append a path to the existing path.
     *
     * @param string $path The path to append
     */
    public function withPath(string $path): static;

    /**
     * Append a name to the existing name.
     *
     * @param string $name The name to append
     */
    public function withName(string $name): static;

    /**
     * Get the redirect path.
     */
    public function getTo(): ?string;

    /**
     * Set the redirect path.
     *
     * @param string|null $to The path to redirect to
     */
    public function setTo(string $to = null): static;

    /**
     * Get the redirect status code.
     */
    public function getCode(): ?int;

    /**
     * Set the redirect status code.
     *
     * @param int|null $code The status code
     */
    public function setCode(int $code = null): static;

    /**
     * Get the request methods.
     */
    public function getMethods(): array;

    /**
     * Set the request methods.
     *
     * @param array $methods The request methods
     */
    public function setMethods(array $methods): static;

    /**
     * Get the regex.
     */
    public function getRegex(): ?string;

    /**
     * Set the regex.
     *
     * @param string|null $regex The regex
     */
    public function setRegex(string $regex = null): static;

    /**
     * Get the parameters.
     *
     * @return Parameter[]
     */
    public function getParameters(): array;

    /**
     * Set the parameters.
     *
     * @param Parameter[]|array $parameters The parameters
     */
    public function setParameters(array $parameters): static;

    /**
     * Set a parameter.
     *
     * @param Parameter $parameter The parameter
     */
    public function setParameter(Parameter $parameter): static;

    /**
     * Add a parameter.
     *
     * @param string                        $name                The name
     * @param string|null                   $regex               [optional] The regex
     * @param CastType|null                 $type                [optional] The cast type
     * @param class-string<Entity>|null     $entity              [optional] The entity class name
     * @param string|null                   $entityColumn        [optional] The entity column to query against
     * @param array|null                    $entityRelationships [optional] The entity relationships
     * @param class-string<BackedEnum>|null $enum                [optional] The enum type
     * @param bool                          $isOptional          [optional] Whether the parameter is optional
     * @param bool                          $shouldCapture       [optional] Whether this parameter should be captured
     * @param mixed                         $default             [optional] The default value for this parameter
     */
    public function addParameter(
        string $name,
        string $regex = null,
        CastType $type = null,
        string $entity = null,
        string $entityColumn = null,
        array $entityRelationships = null,
        string $enum = null,
        bool $isOptional = false,
        bool $shouldCapture = true,
        mixed $default = null
    ): static;

    /**
     * Get the middleware.
     */
    public function getMiddleware(): ?array;

    /**
     * Set the middleware.
     *
     * @param array|null $middleware The middleware
     */
    public function setMiddleware(array $middleware = null): static;

    /**
     * Route with added middleware.
     *
     * @param array $middleware The middleware
     */
    public function withMiddleware(array $middleware): static;

    /**
     * Check whether the route is dynamic.
     */
    public function isDynamic(): bool;

    /**
     * Set the route as dynamic.
     *
     * @param bool $dynamic Whether the route it dynamic
     */
    public function setDynamic(bool $dynamic = true): static;

    /**
     * Get whether the route is secure.
     */
    public function isSecure(): bool;

    /**
     * Set whether the route is secure.
     *
     * @param bool $secure Whether the route is secure
     */
    public function setSecure(bool $secure = true): static;

    /**
     * Get whether the route is a redirect.
     */
    public function isRedirect(): bool;

    /**
     * Set whether the route is a redirect.
     */
    public function setRedirect(bool $redirect): static;
}
