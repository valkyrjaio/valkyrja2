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
 * Interface Driver.
 *
 * @author Melech Mizrachi
 */
interface Driver
{
    /**
     * Initiate a transaction.
     */
    public function beginTransaction(): bool;

    /**
     * In a transaction.
     */
    public function inTransaction(): bool;

    /**
     * Ensure a transaction is in progress.
     */
    public function ensureTransaction(): void;

    /**
     * Commit all items in the transaction.
     */
    public function commit(): bool;

    /**
     * Rollback the previous transaction.
     */
    public function rollback(): bool;

    /**
     * Rollback the previous transaction.
     *
     * @param string $query The query
     */
    public function prepare(string $query): Statement;

    /**
     * Get the last inserted id.
     *
     * @param string|null $table   [optional] The table last inserted into
     * @param string|null $idField [optional] The id field of the table last inserted into
     */
    public function lastInsertId(string $table = null, string $idField = null): string;

    /**
     * Create a new query instance.
     *
     * @param class-string<Entity>|null $entity
     */
    public function createQuery(string $query = null, string $entity = null): Query;

    /**
     * Create a new query builder instance.
     *
     * @param class-string<Entity>|null $entity
     */
    public function createQueryBuilder(string $entity = null, string $alias = null): QueryBuilder;

    /**
     * Create a new retriever instance.
     */
    public function createRetriever(): Retriever;

    /**
     * Get the persister.
     */
    public function getPersister(): Persister;
}
