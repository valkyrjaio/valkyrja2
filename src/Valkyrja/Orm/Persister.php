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
 * Interface Persister.
 *
 * @author Melech Mizrachi
 */
interface Persister
{
    /**
     * Create a new entity.
     *
     * <code>
     *      $persister->create(new Entity(), true | false)
     * </code>
     *
     * @param Entity $entity The entity to create
     * @param bool   $defer  [optional] Whether to defer the creation
     */
    public function create(Entity $entity, bool $defer = true): void;

    /**
     * Update an existing entity.
     *
     * <code>
     *      $persister->save(new Entity(), true | false)
     * </code>
     *
     * @param Entity $entity The entity to save
     * @param bool   $defer  [optional] Whether to defer the save
     */
    public function save(Entity $entity, bool $defer = true): void;

    /**
     * Delete an existing entity.
     *
     * <code>
     *      $persister->delete(new Entity(), true | false)
     * </code>
     *
     * @param Entity $entity The entity to delete
     * @param bool   $defer  [optional] Whether to defer the deletion
     */
    public function delete(Entity $entity, bool $defer = true): void;

    /**
     * Soft delete an existing entity.
     *
     * <code>
     *      $persister->softDelete(new SoftDeleteEntity(), true | false)
     * </code>
     *
     * @param SoftDeleteEntity $entity The entity to soft delete
     * @param bool             $defer  [optional] Whether to defer the soft deletion
     */
    public function softDelete(SoftDeleteEntity $entity, bool $defer = true): void;

    /**
     * Clear all, or a single, deferred entity.
     *
     * <code>
     *      $persister->clear(new Entity())
     * </code>
     *
     * @param Entity|null $entity [optional] The entity instance to remove.
     */
    public function clear(Entity $entity = null): void;

    /**
     * Persist all entities.
     */
    public function persist(): bool;
}
