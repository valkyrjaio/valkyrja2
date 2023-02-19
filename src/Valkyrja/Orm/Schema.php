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
 * Interface Schema.
 *
 * @author Melech Mizrachi
 */
interface Schema
{
    /**
     * Create a new table.
     *
     * @param string $name The table name
     */
    public function createTable(string $name): Table;

    /**
     * Get an existing table.
     *
     * @param string $name The table name
     */
    public function getTable(string $name): Table;

    /**
     * Rename an existing table.
     *
     * @param string $name    The table name
     * @param string $newName The table's new name
     */
    public function renameTable(string $name, string $newName): Table;

    /**
     * Drop a table.
     *
     * @param string $name The table name
     */
    public function dropTable(string $name): Table;

    /**
     * Get the built query string.
     */
    public function getQueryString(): string;

    /**
     * Execute a table query.
     *
     * @param Table $table The table to execute
     */
    public function execute(Table $table): bool;

    /**
     * Execute all pending table queries.
     */
    public function executeAll(): bool;

    /**
     * Get the error if one occurred.
     */
    public function getError(): string;
}
