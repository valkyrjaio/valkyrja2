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

namespace Valkyrja\Orm\Drivers;

use Valkyrja\Orm\Adapter;
use Valkyrja\Orm\Driver as Contract;
use Valkyrja\Orm\Persister;
use Valkyrja\Orm\Query;
use Valkyrja\Orm\QueryBuilder;
use Valkyrja\Orm\Retriever;
use Valkyrja\Orm\Statement;

/**
 * Class Driver.
 *
 * @author Melech Mizrachi
 */
class Driver implements Contract
{
    /**
     * The adapter.
     */
    protected Adapter $adapter;

    /**
     * The config.
     */
    protected array $config;

    /**
     * Driver constructor.
     *
     * @param Adapter $adapter The adapter
     * @param array   $config  The config
     */
    public function __construct(Adapter $adapter, array $config)
    {
        $this->adapter = $adapter;
        $this->config  = $config;
    }

    /**
     * @inheritDoc
     */
    public function beginTransaction(): bool
    {
        return $this->adapter->beginTransaction();
    }

    /**
     * @inheritDoc
     */
    public function inTransaction(): bool
    {
        return $this->adapter->inTransaction();
    }

    /**
     * @inheritDoc
     */
    public function ensureTransaction(): void
    {
        $this->adapter->ensureTransaction();
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return $this->adapter->commit();
    }

    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        return $this->adapter->rollback();
    }

    /**
     * @inheritDoc
     */
    public function prepare(string $query): Statement
    {
        return $this->adapter->prepare($query);
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(string $table = null, string $idField = null): string
    {
        return $this->adapter->lastInsertId($table, $idField);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(string $query = null, string $entity = null): Query
    {
        return $this->adapter->createQuery($query, $entity);
    }

    /**
     * @inheritDoc
     */
    public function createQueryBuilder(string $entity = null, string $alias = null): QueryBuilder
    {
        return $this->adapter->createQueryBuilder($entity, $alias);
    }

    /**
     * @inheritDoc
     */
    public function createRetriever(): Retriever
    {
        return $this->adapter->createRetriever();
    }

    /**
     * @inheritDoc
     */
    public function getPersister(): Persister
    {
        return $this->adapter->getPersister();
    }
}
