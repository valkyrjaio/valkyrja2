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

namespace Valkyrja\Model;

/**
 * Interface ExposableModel.
 *
 * @author Melech Mizrachi
 */
interface ExposableModel extends Model
{
    /**
     * Get a list of exposable properties.
     *
     * @return string[]
     */
    public static function getExposable(): array;

    /**
     * Get model as an array with all properties including exposable ones.
     *
     * @param string ...$properties [optional] An array of properties to return
     */
    public function asExposedArray(string ...$properties): array;

    /**
     * Get model as an array including only all changed properties including exposable ones.
     */
    public function asExposedChangedArray(): array;

    /**
     * Get model as an array including only exposable properties.
     */
    public function asExposedOnlyArray(): array;

    /**
     * Expose hidden properties or all properties.
     *
     * @param string ...$properties The properties to expose
     */
    public function expose(string ...$properties): void;

    /**
     * Unexpose hidden properties or all properties.
     *
     * @param string ...$properties [optional] The properties to unexpose
     */
    public function unexpose(string ...$properties): void;
}
