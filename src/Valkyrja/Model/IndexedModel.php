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
 * Interface IndexedModel.
 *
 * @author Melech Mizrachi
 */
interface IndexedModel extends Model
{
    /**
     * Get the index map for all properties in the model.
     *
     * <code>
     *  [
     *      'propertyName' => 0,
     *      'otherPropertyName' => 1,
     *  ]
     * </code>
     */
    public static function getIndexes(): array;

    /**
     * Get a mapped array from a given indexed array of properties.
     *
     * @param array $properties The properties
     */
    public static function getMappedArrayFromIndexedArray(array $properties = []): array;

    /**
     * Get an indexed array from a given mapped array of properties.
     *
     * @param array $properties The properties
     */
    public static function getIndexedArrayFromMappedArray(array $properties = []): array;

    /**
     * Set properties from an array of properties.
     *
     * @param array $properties The properties
     */
    public static function fromIndexedArray(array $properties): static;

    /**
     * Set properties from an array of properties.
     *
     * @param array $properties The properties
     */
    public function updateIndexedProperties(array $properties): void;

    /**
     * Get a new model with new properties.
     *
     * @param array $properties The properties to modify
     */
    public function withIndexedProperties(array $properties): static;

    /**
     * Get model as an array.
     *
     * @param string ...$properties [optional] An array of properties to return
     */
    public function asIndexedArray(string ...$properties): array;

    /**
     * Get model as an array including only changed properties.
     */
    public function asChangedIndexedArray(): array;

    /**
     * Get all original properties.
     */
    public function asOriginalIndexedArray(): array;
}
