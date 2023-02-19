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

/**
 * Interface Column.
 *
 * @author Melech Mizrachi
 */
interface Column
{
    /**
     * Create the column.
     */
    public function create(): static;

    /**
     * Rename the column.
     *
     * @param string $name The new name
     */
    public function rename(string $name): static;

    /**
     * Drop the column.
     */
    public function drop(): static;

    /**
     * Set the name of the column.
     *
     * @param string $name The column name
     */
    public function setName(string $name): static;

    /**
     * Set the column's type.
     *
     * @param string   $type   The type
     * @param int|null $length [optional] The length constraint for this column
     */
    public function setType(string $type, int $length = null): static;

    /**
     * Set the default value for this column.
     *
     * @param bool|int|string|null $value [optional] The default value
     */
    public function setDefault(bool|int|string $value = null): static;

    /**
     * Set this as a non-nullable column.
     */
    public function isNotNullable(): static;

    /**
     * Set this as a primary key column.
     */
    public function isPrimaryKey(): static;

    /**
     * Set this as a unique column.
     */
    public function isUnique(): static;

    /**
     * Set this as an auto incrementing column.
     */
    public function isAutoIncrementing(): static;

    /**
     * Do only if the table doesn't already exist.
     */
    public function ifNotExists(): static;

    /**
     * Do only if the table exists.
     */
    public function ifExists(): static;
}
