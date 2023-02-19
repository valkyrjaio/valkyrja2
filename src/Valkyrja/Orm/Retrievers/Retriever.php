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

namespace Valkyrja\Orm\Retrievers;

use Valkyrja\Orm\Adapter;
use Valkyrja\Orm\Constants\Statement;
use Valkyrja\Orm\Entity;
use Valkyrja\Orm\Enums\WhereType;
use Valkyrja\Orm\Exceptions\EntityNotFoundException;
use Valkyrja\Orm\Query;
use Valkyrja\Orm\QueryBuilder;
use Valkyrja\Orm\Retriever as Contract;

use function assert;

/**
 * Class Retriever.
 *
 * @author   Melech Mizrachi
 *
 * @implements Contract<Entity>
 */
class Retriever implements Contract
{
    /**
     * The adapter.
     */
    protected Adapter $adapter;

    /**
     * The query builder.
     */
    protected QueryBuilder $queryBuilder;

    /**
     * The query.
     */
    protected Query $query;

    /**
     * The values to bind.
     */
    protected array $values = [];

    /**
     * Retriever constructor.
     *
     * @param Adapter $adapter The adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @inheritDoc
     */
    public function find(string $entity): static
    {
        $this->setQueryProperties($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function findOne(string $entity, int|string $id): static
    {
        $this->setQueryProperties($entity);
        $this->limit(1);

        /** @var Entity $entity */
        $this->where($entity::getIdField(), null, $id);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(string $entity): static
    {
        $this->setQueryProperties($entity, [Statement::COUNT_ALL]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function columns(array $columns): static
    {
        $this->queryBuilder = $this->queryBuilder->select($columns);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function where(string $column, string $operator = null, mixed $value = null, bool $setType = true): static
    {
        $this->queryBuilder->where($column, $operator, $value, $setType);

        if (! ($value instanceof QueryBuilder)) {
            $this->setValue($column, $value);
        }

        return $this;
    }

    /**
     * Start a where clause in parentheses.
     */
    public function startWhereGroup(): static
    {
        $this->queryBuilder->startWhereGroup();

        return $this;
    }

    /**
     * End a where clause in parentheses.
     */
    public function endWhereGroup(): static
    {
        $this->queryBuilder->endWhereGroup();

        return $this;
    }

    /**
     * Add a where type.
     *
     * @param WhereType $type The type
     */
    public function whereType(WhereType $type = WhereType::AND): static
    {
        $this->queryBuilder->whereType($type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function join(
        string $table,
        string $column1,
        string $column2,
        string $operator = null,
        string $type = null,
        bool $isWhere = null
    ): static {
        $this->queryBuilder->join($table, $column1, $column2, $operator, $type, $isWhere);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function groupBy(string $column): static
    {
        $this->queryBuilder->groupBy($column);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orderBy(string $column, string $type = null): static
    {
        $this->queryBuilder->orderBy($column, $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function limit(int $limit): static
    {
        $this->queryBuilder->limit($limit);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offset(int $offset): static
    {
        $this->queryBuilder->offset($offset);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getResult(): array
    {
        $this->prepareResults();

        /** @var Entity[] $results */
        $results = $this->query->getResult();

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getOneOrNull(): ?Entity
    {
        return $this->getResult()[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getOneOrFail(): Entity
    {
        $results = $this->getOneOrNull();

        if ($results === null) {
            throw new EntityNotFoundException('Entity Not Found');
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getCount(): int
    {
        $this->columns([Statement::COUNT_ALL]);

        $this->prepareResults();

        return $this->query->getCount();
    }

    /**
     * @inheritDoc
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * Set query builder and query.
     *
     * @param class-string<Entity> $entity  The entity
     * @param string[]|null        $columns [optional] The columns
     */
    protected function setQueryProperties(string $entity, array $columns = null): void
    {
        assert(is_a($entity, Entity::class, true));

        $this->queryBuilder = $this->adapter->createQueryBuilder($entity)->select($columns);
        $this->query        = $this->adapter->createQuery(null, $entity);
    }

    /**
     * Set a value to bind later.
     *
     * @param string $column The column to bind
     * @param mixed  $value  [optional] The value to bind
     */
    protected function setValue(string $column, mixed $value): void
    {
        $this->values[$column] = $value;
    }

    /**
     * Prepare results.
     */
    protected function prepareResults(): void
    {
        $this->adapter->ensureTransaction();
        $this->query->prepare($this->queryBuilder->getQueryString());
        $this->bindValues();
        $this->query->execute();
    }

    /**
     * Bind values to the query.
     */
    protected function bindValues(): void
    {
        foreach ($this->values as $column => $value) {
            $this->query->bindValue($column, $value);
        }
    }
}
