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

namespace Valkyrja\Orm\Repositories;

use InvalidArgumentException;
use Valkyrja\Orm\Driver;
use Valkyrja\Orm\Entity;
use Valkyrja\Orm\Enums\WhereType;
use Valkyrja\Orm\Exceptions\InvalidEntityException;
use Valkyrja\Orm\Orm;
use Valkyrja\Orm\Persister;
use Valkyrja\Orm\Query;
use Valkyrja\Orm\QueryBuilder;
use Valkyrja\Orm\Repository as Contract;
use Valkyrja\Orm\Retriever;
use Valkyrja\Orm\SoftDeleteEntity;

use function assert;
use function get_class;

/**
 * Class Repository.
 *
 * @author Melech Mizrachi
 *
 * @implements Contract<Entity>
 */
class Repository implements Contract
{
    /**
     * The connection driver.
     */
    protected Driver $driver;

    /**
     * The entity manager.
     */
    protected Orm $orm;

    /**
     * The persister.
     */
    protected Persister $persister;

    /**
     * The retriever.
     */
    protected Retriever $retriever;

    /**
     * The entity to use.
     *
     * @var class-string<Entity>
     */
    protected string $entity;

    /**
     * The relationships to get with each result.
     *
     * @var string[]|null
     */
    protected ?array $relationships = null;

    /**
     * Whether to get relations.
     */
    protected bool $getRelations = false;

    /**
     * Repository constructor.
     *
     * @param Orm                  $manager The orm manager
     * @param Driver               $driver  The driver
     * @param class-string<Entity> $entity  The entity class name
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Orm $manager, Driver $driver, string $entity)
    {
        assert(is_a($entity, Entity::class, true));

        $this->driver    = $driver;
        $this->persister = $this->driver->getPersister();
        $this->orm       = $manager;
        $this->entity    = $entity;
    }

    /**
     * @inheritDoc
     */
    public function find(): static
    {
        $this->retriever = $this->driver->createRetriever()->find($this->entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function findOne(int|string $id): static
    {
        $this->retriever = $this->driver->createRetriever()->findOne($this->entity, $id);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): static
    {
        $this->retriever = $this->driver->createRetriever()->count($this->entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function columns(array $columns): static
    {
        $this->retriever->columns($columns);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function where(string $column, string $operator = null, mixed $value = null, bool $setType = true): static
    {
        $this->retriever->where($column, $operator, $value, $setType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function startWhereGroup(): static
    {
        $this->retriever->startWhereGroup();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function endWhereGroup(): static
    {
        $this->retriever->endWhereGroup();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function whereType(WhereType $type = WhereType::AND): static
    {
        $this->retriever->whereType($type);

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
        $this->retriever->join($table, $column1, $column2, $operator, $type, $isWhere);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orderBy(string $column, string $direction = null): static
    {
        $this->retriever->orderBy($column, $direction);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function limit(int $limit): static
    {
        $this->retriever->limit($limit);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offset(int $offset): static
    {
        $this->retriever->offset($offset);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getResult(): array
    {
        return $this->retriever->getResult();
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
        return $this->retriever->getOneOrFail();
    }

    /**
     * @inheritDoc
     */
    public function getCount(): int
    {
        return $this->retriever->getCount();
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidEntityException
     */
    public function create(Entity $entity, bool $defer = true): void
    {
        $this->validateEntity($entity);

        $this->persister->create($entity, $defer);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidEntityException
     */
    public function save(Entity $entity, bool $defer = true): void
    {
        $this->validateEntity($entity);

        $this->persister->save($entity, $defer);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidEntityException
     */
    public function delete(Entity $entity, bool $defer = true): void
    {
        $this->validateEntity($entity);

        $this->persister->delete($entity, $defer);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidEntityException
     */
    public function softDelete(SoftDeleteEntity $entity, bool $defer = true): void
    {
        $this->validateEntity($entity);

        $this->persister->softDelete($entity, $defer);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidEntityException
     */
    public function clear(Entity $entity = null): void
    {
        if ($entity !== null) {
            $this->validateEntity($entity);
        }

        $this->persister->clear($entity);
    }

    /**
     * @inheritDoc
     */
    public function persist(): bool
    {
        return $this->persister->persist();
    }

    /**
     * @inheritDoc
     */
    public function createQueryBuilder(string $alias = null): QueryBuilder
    {
        return $this->driver->createQueryBuilder($this->entity, $alias);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(string $query): Query
    {
        return $this->driver->createQuery($query, $this->entity);
    }

    /**
     * @inheritDoc
     */
    public function getRetriever(): Retriever
    {
        return $this->retriever;
    }

    /**
     * @inheritDoc
     */
    public function getPersister(): Persister
    {
        return $this->persister;
    }

    /**
     * Validate the passed entity.
     *
     * @param Entity $entity The entity
     *
     * @throws InvalidEntityException
     */
    protected function validateEntity(Entity $entity): void
    {
        if (! ($entity instanceof $this->entity)) {
            throw new InvalidEntityException(
                'This repository expects entities to be instances of '
                . $this->entity
                . '. Entity instanced from '
                . get_class($entity)
                . ' provided instead.'
            );
        }
    }
}
