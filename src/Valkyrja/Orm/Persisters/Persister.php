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

namespace Valkyrja\Orm\Persisters;

use JsonException;
use Valkyrja\Orm\Adapter;
use Valkyrja\Orm\Constants\Statement;
use Valkyrja\Orm\DatedEntity;
use Valkyrja\Orm\Entity;
use Valkyrja\Orm\Exceptions\ExecuteException;
use Valkyrja\Orm\Persister as Contract;
use Valkyrja\Orm\Query;
use Valkyrja\Orm\QueryBuilder;
use Valkyrja\Orm\SoftDeleteEntity;
use Valkyrja\Type\Support\Arr;

use function is_array;
use function is_object;
use function serialize;
use function spl_object_id;
use function strtolower;

/**
 * Class Persister.
 *
 * @author Melech Mizrachi
 */
class Persister implements Contract
{
    /**
     * The adapter.
     */
    protected Adapter $adapter;

    /**
     * The entities awaiting to be committed for creation.
     *
     * @var Entity[]
     */
    protected array $createEntities = [];

    /**
     * The entities awaiting to be committed for saving.
     *
     * @var Entity[]
     */
    protected array $saveEntities = [];

    /**
     * The entities awaiting to be committed for deletion.
     *
     * @var Entity[]
     */
    protected array $deleteEntities = [];

    /**
     * Persister constructor.
     *
     * @param Adapter $connection The adapter
     */
    public function __construct(Adapter $connection)
    {
        $this->adapter = $connection;
    }

    /**
     * @inheritDoc
     *
     * @throws ExecuteException
     * @throws JsonException
     */
    public function create(Entity $entity, bool $defer = true): void
    {
        $this->modifyEntityBeforeCreate($entity);

        if (! $defer) {
            $this->persistEntityThroughTransaction(Statement::INSERT, $entity, $entity->asStorableArray());

            return;
        }

        $id = $this->getIdFromEntity($entity);

        $this->createEntities[$id] = $entity;
    }

    /**
     * @inheritDoc
     *
     * @throws ExecuteException
     * @throws JsonException
     */
    public function save(Entity $entity, bool $defer = true): void
    {
        $this->modifyEntityBeforeSave($entity);

        if (! $defer) {
            $this->persistEntityThroughTransaction(Statement::UPDATE, $entity, $entity->asStorableChangedArray());

            return;
        }

        $id = $this->getIdFromEntity($entity);

        $this->saveEntities[$id] = $entity;
    }

    /**
     * @inheritDoc
     *
     * @throws ExecuteException
     * @throws JsonException
     */
    public function delete(Entity $entity, bool $defer = true): void
    {
        if (! $defer) {
            $this->persistEntityThroughTransaction(Statement::DELETE, $entity);

            return;
        }

        $id = $this->getIdFromEntity($entity);

        $this->deleteEntities[$id] = $entity;
    }

    /**
     * @inheritDoc
     *
     * @throws ExecuteException
     * @throws JsonException
     */
    public function softDelete(SoftDeleteEntity $entity, bool $defer = true): void
    {
        $entity->__set($entity::getIsDeletedField(), true);
        $entity->__set($entity::getDateDeletedField(), $entity::getFormattedDeletedDate());

        $this->save($entity, $defer);
    }

    /**
     * @inheritDoc
     */
    public function clear(Entity $entity = null): void
    {
        if ($entity === null) {
            $this->clearDeferred();

            return;
        }

        $this->clearEntity($entity);
    }

    /**
     * @inheritDoc
     *
     * @throws ExecuteException
     * @throws JsonException
     */
    public function persist(): bool
    {
        // Ensure a transaction is in progress
        $this->adapter->ensureTransaction();

        $this->persistEntities(Statement::INSERT, ...$this->createEntities);
        $this->persistEntities(Statement::UPDATE, ...$this->saveEntities);
        $this->persistEntities(Statement::DELETE, ...$this->deleteEntities);
        $this->clearDeferred();

        return $this->adapter->commit();
    }

    /**
     * Get an id from an entity.
     */
    protected function getIdFromEntity(Entity $entity): int
    {
        return spl_object_id($entity);
    }

    /**
     * Clear a single deferred entity.
     *
     * @param Entity $entity The entity instance to remove.
     */
    protected function clearEntity(Entity $entity): void
    {
        // Get the id of the object
        $id = $this->getIdFromEntity($entity);

        // Unset it
        unset(
            $this->createEntities[$id],
            $this->saveEntities[$id],
            $this->deleteEntities[$id]
        );
    }

    /**
     * Modify an entity before creating it.
     *
     * @param Entity $entity The entity
     */
    protected function modifyEntityBeforeCreate(Entity $entity): void
    {
        if ($entity instanceof DatedEntity) {
            $this->modifyDatedEntityBeforeCreate($entity);
        }
    }

    /**
     * Modify a dated entity before creating it.
     *
     * @param DatedEntity $entity The entity
     */
    protected function modifyDatedEntityBeforeCreate(DatedEntity $entity): void
    {
        $date = $entity::getFormattedDate();

        $entity->__set($entity::getDateCreatedField(), $date);
        $entity->__set($entity::getDateModifiedField(), $date);
    }

    /**
     * Modify an entity before saving it.
     *
     * @param Entity $entity The entity
     */
    protected function modifyEntityBeforeSave(Entity $entity): void
    {
        if ($entity instanceof DatedEntity) {
            $this->modifyDatedEntityBeforeSave($entity);
        }
    }

    /**
     * Modify a dated entity before saving it.
     *
     * @param DatedEntity $entity The entity
     */
    protected function modifyDatedEntityBeforeSave(DatedEntity $entity): void
    {
        $entity->__set($entity::getDateModifiedField(), $entity::getFormattedDate());
    }

    /**
     * Persist an entity through a transaction.
     *
     * @param string $type       The type of persist
     * @param Entity $entity     The entity to persist
     * @param array  $properties [optional] The properties to persist
     *
     * @throws ExecuteException
     * @throws JsonException
     */
    protected function persistEntityThroughTransaction(string $type, Entity $entity, array $properties = []): void
    {
        // Ensure a transaction is in progress
        $this->adapter->ensureTransaction();

        $this->persistEntity($type, $entity, $properties);

        $this->adapter->commit();
    }

    /**
     * Persist an entity.
     *
     * @param string $type       The type of persist
     * @param Entity $entity     The entity to persist
     * @param array  $properties [optional] The properties to persist
     *
     * @throws ExecuteException
     * @throws JsonException
     */
    protected function persistEntity(string $type, Entity $entity, array $properties = []): void
    {
        // The id field
        $idField = $entity::getIdField();

        // Get the query builder
        $queryBuilder = $this->getQueryBuilder($type, $entity, $properties);
        // Get the query
        $query = $this->getQuery($queryBuilder, $type, $idField, $entity->{$idField}, $properties);

        // Execute the query
        $this->executeQuery($query);
        // Set the entity id field after persisting
        $this->setEntityIdFieldAfterPersist($entity, $idField);
    }

    /**
     * Get the query builder.
     *
     * @param string $type       The type of persist
     * @param Entity $entity     The entity to persist
     * @param array  $properties The properties to persist
     */
    protected function getQueryBuilder(string $type, Entity $entity, array $properties): QueryBuilder
    {
        // Create a new query
        $queryBuilder = $this->createQueryBuilder();

        // Set the table in the query builder
        $this->setTableInQueryBuilder($queryBuilder, $entity);
        // Set the type in the query builder
        $this->setTypeInQueryBuilder($queryBuilder, $type);
        // Set the id field where clause in the query builder
        $this->setIdFieldInQueryBuilder($queryBuilder, $entity, $type);
        // Set the properties in the query builder
        $this->setQueryBuilderProperties($queryBuilder, $properties, $type);

        return $queryBuilder;
    }

    /**
     * Get the query.
     *
     * @param QueryBuilder $queryBuilder The query builder
     * @param string       $type         The type of persist
     * @param string       $idField      The id field
     * @param int|string   $id           The id
     * @param array        $properties   The properties to persist
     *
     * @throws JsonException
     */
    protected function getQuery(
        QueryBuilder $queryBuilder,
        string $type,
        string $idField,
        int|string $id,
        array $properties
    ): Query {
        // Create a new query with the query builder
        $query = $this->createQueryFromQueryBuilder($queryBuilder);

        // Bind the id in the query
        $this->bindIdValueInQuery($query, $type, $idField, $id);
        // Set the properties.
        $this->bindQueryProperties($query, $properties, $type);

        return $query;
    }

    /**
     * Create a query builder to persist an entity.
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->adapter->createQueryBuilder();
    }

    /**
     * Set the table to use in a query builder for a given entity.
     *
     * @param QueryBuilder $queryBuilder The query builder
     * @param Entity       $entity       The entity
     */
    protected function setTableInQueryBuilder(QueryBuilder $queryBuilder, Entity $entity): void
    {
        $queryBuilder->table($entity::getTableName());
    }

    /**
     * Set the type of query in the query builder.
     *
     * @param QueryBuilder $queryBuilder The query builder
     * @param string       $type         The type of persist
     */
    protected function setTypeInQueryBuilder(QueryBuilder $queryBuilder, string $type): void
    {
        $queryBuilder->{strtolower($type)}();
    }

    /**
     * Set where in a query builder for a specific entity.
     *
     * @param QueryBuilder $queryBuilder The query builder
     * @param Entity       $entity       The entity to persist
     * @param string       $type         The type of persist
     */
    protected function setIdFieldInQueryBuilder(QueryBuilder $queryBuilder, Entity $entity, string $type): void
    {
        // If this type is an insert we won't have an id yet for a where clause
        if ($type === Statement::INSERT) {
            // So back out
            return;
        }

        // Set the id for the where clause
        $queryBuilder->where($entity::getIdField());
    }

    /**
     * Set properties for save, delete, or create queries.
     *
     * @param QueryBuilder $queryBuilder The query builder
     * @param array        $properties   The properties to persist
     * @param string       $type         The type of persist
     */
    protected function setQueryBuilderProperties(QueryBuilder $queryBuilder, array $properties, string $type): void
    {
        // If this is a delete statement we only needed the id field which was set prior
        if ($type === Statement::DELETE) {
            // So back out in this case and don't iterate through properties
            return;
        }

        // Iterate through the properties
        foreach ($properties as $column => $value) {
            $this->setQueryBuilderProperty($queryBuilder, $column, $value);
        }
    }

    /**
     * Set properties for save, delete, or create queries.
     *
     * @param QueryBuilder $queryBuilder The query builder
     * @param string       $column       The column name
     * @param mixed        $value        The property value
     */
    protected function setQueryBuilderProperty(QueryBuilder $queryBuilder, string $column, mixed $value): void
    {
        // Set the column and param name
        $queryBuilder->set($column);
    }

    /**
     * Create a query to persist an entity given a query builder.
     *
     * @param QueryBuilder $queryBuilder The query builder
     */
    protected function createQueryFromQueryBuilder(QueryBuilder $queryBuilder): Query
    {
        return $this->adapter->createQuery($queryBuilder->getQueryString());
    }

    /**
     * Bind an id value in a query.
     *
     * @param Query      $query   The query
     * @param string     $type    The type of persist
     * @param string     $idField The id field
     * @param int|string $id      The id
     */
    protected function bindIdValueInQuery(Query $query, string $type, string $idField, int|string $id): void
    {
        // If this type is an insert then there was no reason to add a where clause so we should not bind the value
        if ($type === Statement::INSERT) {
            // So get out
            return;
        }

        // Bind the id value for the where clause
        $query->bindValue($idField, $id);
    }

    /**
     * Set properties for save, create, or delete statements.
     *
     * @param Query  $query      The query
     * @param array  $properties The properties to persist
     * @param string $type       The type of persist
     *
     * @throws JsonException
     */
    protected function bindQueryProperties(Query $query, array $properties, string $type): void
    {
        // If this is a delete statement we only needed the id field which was set prior
        if ($type === Statement::DELETE) {
            // So back out in this case and don't iterate through properties
            return;
        }

        // Iterate through the properties
        foreach ($properties as $column => $value) {
            $this->bindQueryProperty($query, $column, $value);
        }
    }

    /**
     * Set property for save, create, or delete statements.
     *
     * @param Query  $query  The query
     * @param string $column The column name
     * @param mixed  $value  The property value
     *
     * @throws JsonException
     */
    protected function bindQueryProperty(Query $query, string $column, mixed $value): void
    {
        // If the property is an object, then serialize it
        if (is_object($value)) {
            $value = serialize($value);
        } // Otherwise json encode if its an array
        elseif (is_array($value)) {
            $value = Arr::toString($value);
        }

        // Bind property
        $query->bindValue($column, $value);
    }

    /**
     * @param Query $query The query
     *
     * @throws ExecuteException
     */
    protected function executeQuery(Query $query): void
    {
        // If the execute failed
        if (! $query->execute()) {
            // Throw a fail exception
            throw new ExecuteException($query->getError());
        }
    }

    /**
     * Set the entity id field after persisting.
     *
     * @param Entity $entity  The entity
     * @param string $idField The id field
     */
    protected function setEntityIdFieldAfterPersist(Entity $entity, string $idField): void
    {
        if (
            ! $entity->__isset($idField)
            && $lastInsertId = $this->adapter->lastInsertId($entity::getTableName(), $idField)
        ) {
            $entity->__set($idField, $lastInsertId);
        }
    }

    /**
     * Clear deferred entities.
     */
    protected function clearDeferred(): void
    {
        $this->createEntities = [];
        $this->saveEntities   = [];
        $this->deleteEntities = [];
    }

    /**
     * Persist a list of entities.
     *
     * @param string $type        The type of persist
     * @param Entity ...$entities The entities to persist
     *
     * @throws ExecuteException
     * @throws JsonException
     */
    protected function persistEntities(string $type, Entity ...$entities): void
    {
        // Iterate through the entities
        foreach ($entities as $entity) {
            // Persist the entity
            $this->persistEntity($type, $entity);
        }
    }
}
