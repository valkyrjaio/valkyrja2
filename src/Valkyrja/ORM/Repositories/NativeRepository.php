<?php

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\ORM\Repositories;

use InvalidArgumentException;
use Valkyrja\ORM\Entity;
use Valkyrja\ORM\EntityManager;
use Valkyrja\ORM\Exceptions\InvalidEntityException;
use Valkyrja\ORM\Query;
use Valkyrja\ORM\QueryBuilder;
use Valkyrja\ORM\Repository;

/**
 * Class NativeRepository.
 *
 * @author Melech Mizrachi
 */
class NativeRepository implements Repository
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected EntityManager $entityManager;

    /**
     * The entity to use.
     *
     * @var string|Entity
     */
    protected string $entity;

    /**
     * The table to use.
     *
     * @var string
     */
    protected string $table;

    /**
     * MySQLRepository constructor.
     *
     * @param EntityManager $entityManager
     * @param string        $entity
     *
     * @throws InvalidArgumentException
     */
    public function __construct(EntityManager $entityManager, string $entity)
    {
        $this->entityManager = $entityManager;
        $this->entity        = $entity;
        $this->table         = $this->entity::getTable();
    }

    /**
     * Find a single entity given its id.
     *
     * @param string|int $id
     * @param bool|null  $getRelations
     *
     * @return Entity|null
     */
    public function find($id, bool $getRelations = null): ?Entity
    {
        return $this->entityManager->find($this->entity, $id, $getRelations);
    }

    /**
     * Find entities by given criteria.
     * <code>
     *      $repository
     *          ->findBy(
     *              [
     *                  'column'  => 'value',
     *                  'column2' => 'value2',
     *              ],
     *              [
     *                  'column'
     *                  'column2' => OrderBy::ASC,
     *                  'column3' => OrderBy::DESC,
     *              ],
     *              1,
     *              1
     *          )
     * </code>.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @param array|null $columns
     * @param bool|null  $getRelations
     *
     * @return Entity[]
     */
    public function findBy(
        array $criteria,
        array $orderBy = null,
        int $limit = null,
        int $offset = null,
        array $columns = null,
        bool $getRelations = null
    ): array {
        return $this->entityManager->findBy($this->entity, $criteria, $orderBy, $limit, $offset, $columns, $getRelations);
    }

    /**
     * Find entities by given criteria.
     * <code>
     *      $repository
     *          ->findBy(
     *              [
     *                  'column'
     *                  'column2' => OrderBy::ASC,
     *                  'column3' => OrderBy::DESC,
     *              ]
     *          )
     * </code>.
     *
     * @param array      $orderBy
     * @param array|null $columns
     * @param bool|null  $getRelations
     *
     * @return Entity[]
     */
    public function findAll(array $orderBy = null, array $columns = null, bool $getRelations = null): array
    {
        return $this->entityManager->findAll($this->entity, $orderBy, $columns, $getRelations);
    }

    /**
     * Count all the results of given criteria.
     * <code>
     *      $repository
     *          ->count(
     *              [
     *                  'column'  => 'value',
     *                  'column2' => 'value2',
     *              ]
     *          )
     * </code>.
     *
     * @param array $criteria
     *
     * @return int
     */
    public function count(array $criteria): int
    {
        return $this->entityManager->count($this->entity, $criteria);
    }

    /**
     * Create a new model.
     * <code>
     *      $this->create(Entity::class)
     * </code>.
     *
     * @param Entity $entity
     *
     * @throws InvalidEntityException
     *
     * @return void
     */
    public function create(Entity $entity): void
    {
        $this->validateEntity($entity);

        $this->entityManager->create($entity);
    }

    /**
     * Save an existing model given criteria to find. If no criteria specified uses all model properties.
     * <code>
     *      $this->save(Entity::class)
     * </code>.
     *
     * @param Entity $entity
     *
     * @throws InvalidEntityException
     *
     * @return void
     */
    public function save(Entity $entity): void
    {
        $this->validateEntity($entity);

        $this->entityManager->save($entity);
    }

    /**
     * Delete an existing model.
     * <code>
     *      $this->delete(Entity::class)
     * </code>.
     *
     * @param Entity $entity
     *
     * @throws InvalidEntityException
     *
     * @return void
     */
    public function delete(Entity $entity): void
    {
        $this->validateEntity($entity);

        $this->entityManager->delete($entity);
    }

    /**
     * Get the last inserted id.
     *
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->entityManager->lastInsertId();
    }

    /**
     * Get a new query builder instance.
     *
     * @param string|null $alias
     *
     * @return QueryBuilder
     */
    public function queryBuilder(string $alias = null): QueryBuilder
    {
        return $this->entityManager->queryBuilder($this->entity, $alias);
    }

    /**
     * Start a query.
     *
     * @param string $query
     *
     * @return Query
     */
    public function query(string $query): Query
    {
        return $this->entityManager->query($query, $this->entity);
    }

    /**
     * Validate the passed entity.
     *
     * @param Entity $entity
     *
     * @throws InvalidEntityException
     *
     * @return void
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
