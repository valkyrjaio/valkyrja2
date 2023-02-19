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
 * Interface Table.
 *
 * @author Melech Mizrachi
 */
interface Table
{
    /**
     * Create the table.
     */
    public function create(): static;

    /**
     * Rename the table.
     *
     * @param string $name The new name
     */
    public function rename(string $name): static;

    /**
     * Drop the table.
     */
    public function drop(): static;

    /**
     * Set the name of the table.
     *
     * @param string $name The table name
     */
    public function setName(string $name): static;

    /**
     * Do only if the table doesn't already exist.
     */
    public function ifNotExists(): static;

    /**
     * Do only if the table exists.
     */
    public function ifExists(): static;

    /**
     * Create a new column.
     *
     * @param string $name The column name
     */
    public function createColumn(string $name): Column;

    /**
     * Change an existing column.
     *
     * @param string $name The column name
     */
    public function changeColumn(string $name): Column;

    /**
     * Drop an existing column.
     *
     * @param string $name The column name
     */
    public function dropColumn(string $name): Column;

    /**
     * Create a new index.
     *
     * @param string $name The index name
     */
    public function createIndex(string $name): Index;

    /**
     * Change an existing index.
     *
     * @param string $name The index name
     */
    public function changeIndex(string $name): Index;

    /**
     * Drop an existing index.
     *
     * @param string $name The index name
     */
    public function dropIndex(string $name): Index;

    /**
     * Create a new constraint.
     *
     * @param string $name The constraint name
     */
    public function createConstraint(string $name): Constraint;

    /**
     * Change an existing constraint.
     *
     * @param string $name The constraint name
     */
    public function changeConstraint(string $name): Constraint;

    /**
     * Drop an existing constraint.
     *
     * @param string $name The constraint name
     */
    public function dropConstraint(string $name): Constraint;

    /**
     * Get the built query string.
     */
    public function getQueryString(): string;
}
