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
 * Interface Statement.
 *
 * @author Melech Mizrachi
 */
interface Statement
{
    /**
     * Bind a value.
     */
    public function bindValue(string $parameter, mixed $value): bool;

    /**
     * Execute the statement.
     */
    public function execute(): bool;

    /**
     * Get a column's meta information.
     *
     * @param int $columnNumber The column index in relation to the query statement
     */
    public function getColumnMeta(int $columnNumber): array;

    /**
     * Fetch the results.
     */
    public function fetch(): array;

    /**
     * Fetch a single column.
     */
    public function fetchColumn(int $columnNumber = 0): mixed;

    /**
     * Fetch all the results.
     */
    public function fetchAll(): array;

    /**
     * Fetch the results as an object.
     */
    public function fetchObject(string $className = 'stdClass'): object;

    /**
     * The number of rows returned.
     */
    public function rowCount(): int;

    /**
     * Count of columns returned.
     */
    public function columnCount(): int;

    /**
     * The error code.
     */
    public function errorCode(): string;

    /**
     * The error message.
     */
    public function errorMessage(): ?string;
}
