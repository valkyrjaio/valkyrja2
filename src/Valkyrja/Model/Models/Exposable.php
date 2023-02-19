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

namespace Valkyrja\Model\Models;

use Closure;

use function is_string;

/**
 * Trait Exposable.
 *
 * @author Melech Mizrachi
 */
trait Exposable
{
    /**
     * Properties that are exposable.
     *
     * @var string[]
     */
    protected static array $exposable = [];

    /**
     * The properties to expose.
     *
     * @var array<string, bool>
     */
    protected array $__exposed = [];

    /**
     * @inheritDoc
     *
     * @return string[]
     */
    public static function getExposable(): array
    {
        return static::$exposable;
    }

    /**
     * @inheritDoc
     */
    public function asExposedArray(string ...$properties): array
    {
        return $this->__arrayWithExposed('asArray', ...$properties);
    }

    /**
     * @inheritDoc
     */
    public function asExposedChangedArray(): array
    {
        return $this->__arrayWithExposed('asChangedArray');
    }

    /**
     * @inheritDoc
     */
    public function asExposedOnlyArray(): array
    {
        return $this->__arrayWithExposed(fn () => $this->__exposed);
    }

    /**
     * @inheritDoc
     */
    public function expose(string ...$properties): void
    {
        foreach ($properties as $property) {
            $this->__exposed[$property] = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function unexpose(string ...$properties): void
    {
        if (empty($properties)) {
            $this->__exposed = [];

            return;
        }

        foreach ($properties as $property) {
            unset($this->__exposed[$property]);
        }
    }

    /**
     * Get all properties.
     */
    protected function __allProperties(): array
    {
        return array_merge(parent::__allProperties(), $this->__exposed);
    }

    /**
     * Remove internal model properties from an array of properties.
     *
     * @param array $properties The properties
     */
    protected function __removeInternalProperties(array &$properties): void
    {
        parent::__removeInternalProperties($properties);

        unset($properties['__exposed']);
    }

    /**
     * Get an array with exposed properties.
     *
     * @param Closure|string $callable      The callable
     * @param string         ...$properties The properties
     */
    protected function __arrayWithExposed(Closure|string $callable, string ...$properties): array
    {
        $exposable = static::getExposable();

        $this->expose(...$exposable);

        if (is_string($callable)) {
            $array = $this->$callable(...$properties);
        } else {
            $array = $callable(...$properties);
        }

        $this->unexpose(...$exposable);

        return $array;
    }
}
