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

namespace Valkyrja\Orm\Config;

use Valkyrja\Config\Config as Model;

/**
 * Class Cache.
 *
 * @author Melech Mizrachi
 */
class Cache extends Model
{
    /**
     * The entities mapped to tables.
     *
     * <code>
     *  [
     *      Entity::class => [
     *          'table'         => 'table_name',
     *          'idField'       => 'id',
     *          'fields'        => [
     *              'id',
     *              'data',
     *              'date_modified',
     *              'date_created',
     *          ],
     *          'castings'      => [
     *              'a_serialized_object' => PropertyType::OBJECT,
     *              'a_stringified_array' => PropertyType::ARRAY,
     *              'data_as_json_string' => PropertyType::JSON,
     *          ],
     *          'relationships' => [
     *              'relationship_property_name' => Entity::class,
     *          ],
     *      ],
     *  ]
     * </code>
     */
    public array $entitiesMap = [];

    /**
     * The entities with all pertinent information.
     */
    public array $entities = [];
}
