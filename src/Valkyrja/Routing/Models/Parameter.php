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

use Valkyrja\Model\Data\Cast;
use Valkyrja\Model\Models\Model;
use Valkyrja\Routing\Constants\Regex;
use Valkyrja\Routing\Data\EntityCast;

/**
 * Class Parameter.
 *
 * @author Melech Mizrachi
 */
class Parameter extends Model
{
    /**
     * @inheritDoc
     */
    protected static bool $shouldSetOriginalProperties = false;

    /**
     * Parameter constructor.
     *
     * @param string    $name          The name
     * @param string    $regex         The regex
     * @param Cast|null $cast          [optional] The casting if any
     * @param bool      $isOptional    [optional] Whether this parameter is optional
     * @param bool      $shouldCapture [optional] Whether this parameter should be captured
     * @param mixed     $default       [optional] The default value for this parameter
     */
    public function __construct(
        protected string $name = '',
        protected string $regex = Regex::ANY,
        protected Cast|null $cast = null,
        protected bool $isOptional = false,
        protected bool $shouldCapture = true,
        protected mixed $default = null,
    ) {
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name The name
     *
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the regex.
     *
     * @return string
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * Set the regex.
     *
     * @param string $regex The regex
     *
     * @return static
     */
    public function setRegex(string $regex): static
    {
        $this->regex = $regex;

        return $this;
    }

    /**
     * Get the cast.
     *
     * @return Cast|null
     */
    public function getCast(): ?Cast
    {
        return $this->cast;
    }

    /**
     * Set the cast.
     *
     * @param Cast|array|null $cast The cast
     *
     * @return static
     */
    public function setCast(Cast|array|null $cast = null): static
    {
        if (is_array($cast)) {
            $type          = $cast['type'];
            $isArray       = $cast['isArray'] ?? false;
            $convert       = $cast['convert'] ?? true;
            $column        = $cast['column'] ?? null;
            $relationships = $cast['relationships'] ?? null;

            $cast = ($column !== null)
                ? new EntityCast(
                                   $type,
                    column       : $column,
                    relationships: $relationships,
                    convert      : $convert,
                    isArray      : $isArray
                )
                : new Cast(
                    $type,
                    $convert,
                    $isArray
                );
        }

        $this->cast = $cast;

        return $this;
    }

    /**
     * Get whether this parameter is optional.
     *
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * Set whether this parameter is optional.
     *
     * @param bool $isOptional Whether this parameter is optional
     *
     * @return static
     */
    public function setIsOptional(bool $isOptional): static
    {
        $this->isOptional = $isOptional;

        return $this;
    }

    /**
     * Get whether this parameter should be captured.
     *
     * @return bool
     */
    public function shouldCapture(): bool
    {
        return $this->shouldCapture;
    }

    /**
     * Set whether this parameter should be captured.
     *
     * @param bool $shouldCapture Whether this parameter should be captured
     *
     * @return static
     */
    public function setShouldCapture(bool $shouldCapture): static
    {
        $this->shouldCapture = $shouldCapture;

        return $this;
    }

    /**
     * Get the default value.
     *
     * @return mixed
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * Set the default value.
     *
     * @param mixed $default The default value
     *
     * @return static
     */
    public function setDefault(mixed $default = null): static
    {
        $this->default = $default;

        return $this;
    }
}
