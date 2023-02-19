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
 * Interface Constraint.
 *
 * @author Melech Mizrachi
 */
interface Constraint
{
    /**
     * Create the constraint.
     */
    public function create(): static;

    /**
     * Rename the constraint.
     *
     * @param string $name The new name
     */
    public function rename(string $name): static;

    /**
     * Drop the constraint.
     */
    public function drop(): static;

    /**
     * Set the name of the constraint.
     *
     * @param string $name The constraint name
     */
    public function setName(string $name): static;

    /**
     * Add a column to the constraint.
     *
     * @param string $name The column name
     */
    public function addColumn(string $name): static;

    /**
     * Set this as a primary key constraint.
     */
    public function isPrimaryKey(): static;

    /**
     * Do only if the table doesn't already exist.
     */
    public function ifNotExists(): static;

    /**
     * Do only if the table exists.
     */
    public function ifExists(): static;
}
