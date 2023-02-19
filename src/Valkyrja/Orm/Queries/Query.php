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

namespace Valkyrja\Orm\Queries;

use stdClass;
use Valkyrja\Orm\Adapter;
use Valkyrja\Orm\Entity;
use Valkyrja\Orm\Exceptions\NotFoundException;
use Valkyrja\Orm\Query as QueryContract;
use Valkyrja\Orm\Statement;
use Valkyrja\Orm\Support\Helpers;

use function assert;
use function is_array;

/**
 * Class Query.
 *
 * @author Melech Mizrachi
 */
class Query implements QueryContract
{
    /**
     * The adapter.
     */
    protected Adapter $adapter;

    /**
     * The statement.
     */
    protected Statement $statement;

    /**
     * The table to query on.
     */
    protected ?string $table = null;

    /**
     * The entity to query with.
     *
     * @var class-string<Entity>|null
     */
    protected ?string $entity = null;

    /**
     * Query constructor.
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @inheritDoc
     */
    public function table(string $table): static
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function entity(string $entity): static
    {
        assert(is_a($entity, Entity::class, true));

        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prepare(string $query): static
    {
        /** @var class-string<Entity> $entity */
        if (($entity = $this->entity) !== null) {
            $query = str_replace($entity, $entity::getTableName(), $query);
        }

        $this->statement = $this->adapter->prepare($query);

        if ($this->table !== null) {
            $this->bindValue('table', $this->table);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bindValue(string $property, mixed $value): static
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $this->bindValue($property . $key, $item);
            }

            return $this;
        }

        // And bind each value to the column
        $this->statement->bindValue(Helpers::getColumnForValueBind($property), $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(): bool
    {
        return $this->statement->execute();
    }

    /**
     * @inheritDoc
     */
    public function getResult(): array
    {
        $results = $this->statement->fetchAll();

        /** @var class-string<Entity>|null $entity */
        $entity = $this->entity;

        // If there is no entity specified just return the results
        if ($entity === null) {
            return array_map(
                static function (array $data): stdClass {
                    /** @var stdClass $object */
                    $object = (object) $data;

                    return $object;
                },
                $results
            );
        }

        return array_map(
            static function (array $data) use ($entity): Entity {
                return $entity::fromArray($data);
            },
            $results
        );
    }

    /**
     * @inheritDoc
     */
    public function getOneOrNull(): Entity|stdClass|null
    {
        return $this->getResult()[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getOneOrFail(): object
    {
        $results = $this->getOneOrNull();

        if ($results === null) {
            throw new NotFoundException('Result Not Found');
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getCount(): int
    {
        $results = $this->statement->fetchAll();

        return (int) ($results[0]['COUNT(*)'] ?? $results[0]['count'] ?? 0);
    }

    /**
     * @inheritDoc
     */
    public function getError(): string
    {
        return $this->statement->errorMessage() ?? 'An unknown error occurred.';
    }
}
