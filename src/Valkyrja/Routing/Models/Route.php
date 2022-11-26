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

namespace Valkyrja\Routing\Models;

use InvalidArgumentException;
use Valkyrja\Dispatcher\Models\Dispatch;
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Routing\Enums\CastType;
use Valkyrja\Routing\Route as Contract;
use Valkyrja\Support\Model\Enums\CastType as ModelCastType;
use Valkyrja\Support\Type\Str;

/**
 * Class Route.
 *
 * @author Melech Mizrachi
 */
class Route extends Dispatch implements Contract
{
    /**
     * @inheritDoc
     */
    protected static array $castings = [
        'parameters' => [ModelCastType::model, [Parameter::class]],
    ];

    /**
     * The path for this route.
     *
     * @var string|null
     */
    public ?string $path;

    /**
     * The redirect path for this route.
     *
     * @var string|null
     */
    public ?string $to;

    /**
     * The redirect status code for this route.
     *
     * @var int|null
     */
    public ?int $code;

    /**
     * The request methods for this route.
     *
     * @var array
     */
    public array $methods = [
        RequestMethod::GET,
        RequestMethod::HEAD,
    ];

    /**
     * The regex for dynamic routes.
     *
     * @var string|null
     */
    public ?string $regex;

    /**
     * The dynamic parameters
     *
     * @var Parameter[]
     */
    public array $parameters;

    /**
     * The middleware for this route.
     *
     * @var array|null
     */
    public ?array $middleware;

    /**
     * Whether the route is dynamic.
     *
     * @var bool
     */
    public bool $dynamic = false;

    /**
     * Whether the route is secure.
     *
     * @var bool
     */
    public bool $secure = false;

    /**
     * Whether the route is a redirect.
     *
     * @var bool
     */
    public bool $redirect = false;

    /**
     * @inheritDoc
     */
    public function getPath(): ?string
    {
        return $this->path ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path): static
    {
        $this->dynamic = Str::contains($path, '{');

        $this->path = $path;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTo(): ?string
    {
        return $this->to ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setTo(string $to = null): static
    {
        $this->redirect = $to !== null;

        $this->to = $to;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): ?int
    {
        return $this->code ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setCode(int $code = null): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @inheritDoc
     */
    public function setMethods(array $methods): static
    {
        if (array_diff($methods, RequestMethod::ANY)) {
            throw new InvalidArgumentException('Invalid request methods set');
        }

        $this->methods = $methods;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRegex(): ?string
    {
        return $this->regex ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setRegex(string $regex = null): static
    {
        $this->regex = $regex;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParameters(): array
    {
        return $this->parameters ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setParameters(array $parameters): static
    {
        $this->__setParameters(...$parameters);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParameter(Parameter $parameter): static
    {
        $this->parameters ??= [];

        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * @inheritDoc
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
    ): static {
        return $this->setParameter(
            new Parameter(
                name               : $name,
                regex              : $regex,
                type               : $type,
                entity             : $entity,
                entityColumn       : $entityColumn,
                entityRelationships: $entityRelationships,
                enum               : $enum,
                isOptional         : $isOptional,
                shouldCapture      : $shouldCapture,
                default            : $default
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getMiddleware(): ?array
    {
        return $this->middleware ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setMiddleware(array $middleware = null): static
    {
        $this->middleware = $middleware;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withMiddleware(array $middleware): static
    {
        $this->middleware = array_merge($this->middleware ?? [], $middleware);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDynamic(): bool
    {
        return $this->dynamic;
    }

    /**
     * @inheritDoc
     */
    public function setDynamic(bool $dynamic = true): static
    {
        $this->dynamic = $dynamic;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @inheritDoc
     */
    public function setSecure(bool $secure = true): static
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRedirect(): bool
    {
        return $this->redirect;
    }

    /**
     * @inheritDoc
     */
    public function setRedirect(bool $redirect): static
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * Set the parameters.
     *
     * @param Parameter[] $parameters The parameters
     *
     * @return void
     */
    protected function __setParameters(Parameter ...$parameters): void
    {
        $this->parameters = $parameters;
    }
}
