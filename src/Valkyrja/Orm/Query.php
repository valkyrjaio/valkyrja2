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

use stdClass;
use Valkyrja\Orm\Exceptions\NotFoundException;

/**
 * Interface Query.
 *
 * @author Melech Mizrachi
 */
interface Query
{
    /**
     * Set the table to query on.
     */
    public function table(string $table): static;

    /**
     * Set the entity to query with.
     *
     * @param class-string<Entity> $entity
     */
    public function entity(string $entity): static;

    /**
     * Prepare the query.
     */
    public function prepare(string $query): static;

    /**
     * Bind a value.
     */
    public function bindValue(string $property, mixed $value): static;

    /**
     * Execute the query.
     */
    public function execute(): bool;

    /**
     * Get the result.
     *
     * @return Entity[]|stdClass[]
     */
    public function getResult(): array;

    /**
     * Get one or null.
     */
    public function getOneOrNull(): Entity|stdClass|null;

    /**
     * Get one or fail.
     *
     * @throws NotFoundException
     *
     * @return Entity|object
     */
    public function getOneOrFail(): object;

    /**
     * Get count results.
     */
    public function getCount(): int;

    /**
     * Get the error if one occurred.
     */
    public function getError(): string;
}
