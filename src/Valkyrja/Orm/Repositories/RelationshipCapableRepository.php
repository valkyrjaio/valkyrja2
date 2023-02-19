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

use Valkyrja\Orm\Entity;
use Valkyrja\Type\Support\StrCase;

/**
 * Trait RelationshipCapableRepository.
 *
 * @author Melech Mizrachi
 */
trait RelationshipCapableRepository
{
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
     * @inheritDoc
     */
    public function find(): static
    {
        $this->resetRelationships();

        parent::find();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function findOne(int|string $id): static
    {
        $this->resetRelationships();

        parent::findOne($id);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): static
    {
        $this->resetRelationships();

        parent::count();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withRelationships(array $relationships = null): static
    {
        $this->getRelations  = true;
        $this->relationships = $relationships;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withAllRelationships(): static
    {
        $this->getRelations  = true;
        $this->relationships = $this->entity::getRelationshipProperties();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withoutRelationships(): static
    {
        $this->resetRelationships();

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return Entity[]
     */
    public function getResult(): array
    {
        $results = parent::getResult();

        $this->setRelationshipsOnEntities(...$results);

        return $results;
    }

    /**
     * Reset the relationship properties.
     */
    protected function resetRelationships(): void
    {
        $this->getRelations  = false;
        $this->relationships = null;
    }

    /**
     * Set relationships on the entities from results.
     *
     * @param Entity ...$entities The entities to add relationships to
     */
    protected function setRelationshipsOnEntities(Entity ...$entities): void
    {
        $relationships = $this->relationships;

        if (empty($relationships) || ! $this->getRelations || empty($entities)) {
            return;
        }

        // Iterate through the rows found
        foreach ($entities as $entity) {
            // Get the entity relations
            $this->setRelationshipsOnEntity($relationships, $entity);
        }
    }

    /**
     * Set relationships on an entity.
     *
     * @param array  $relationships The relationships to set
     * @param Entity $entity        The entity
     */
    protected function setRelationshipsOnEntity(array $relationships, Entity $entity): void
    {
        // Iterate through the rows found
        foreach ($relationships as $relationship) {
            // Set the entity relations
            $this->setRelationship($entity, $relationship);
        }
    }

    /**
     * Set a relationship property.
     *
     * @param Entity $entity       The entity
     * @param string $relationship The relationship to set
     */
    protected function setRelationship(Entity $entity, string $relationship): void
    {
        $methodName = 'set' . StrCase::toStudlyCase($relationship) . 'Relationship';

        if (method_exists($this, $methodName)) {
            $this->$methodName($entity);
        }
    }
}
