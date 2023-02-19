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

namespace Valkyrja\Orm;

use Valkyrja\Model\CastableModel;
use Valkyrja\Model\ExposableModel;

/**
 * Interface Entity.
 *
 * @author Melech Mizrachi
 */
interface Entity extends CastableModel, ExposableModel
{
    /**
     * Get the table.
     */
    public static function getTableName(): string;

    /**
     * Get the id field.
     */
    public static function getIdField(): string;

    /**
     * Get the repository to use for this entity.
     *
     * @return class-string<Repository>|null
     */
    public static function getRepository(): ?string;

    /**
     * Get the connection to use for this entity.
     */
    public static function getConnection(): ?string;

    /**
     * Entity relationship properties.
     *
     * <code>
     *      [
     *          'property_name',
     *          'property_name_alt',
     *          ...
     *      ]
     * </code>
     *
     * @return string[]
     */
    public static function getRelationshipProperties(): array;

    /**
     * Get a list of fields we do not want for storage.
     *
     * @return string[]
     */
    public static function getUnStorableFields(): array;

    /**
     * Get the entity as an array for saving to the data store.
     *
     * @param string ...$properties [optional] An array of properties to return
     */
    public function asStorableArray(string ...$properties): array;

    /**
     * Get the entity as an array for saving to the data store including only changed properties.
     */
    public function asStorableChangedArray(): array;
}
