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

namespace Valkyrja\Orm\QueryBuilders;

use Valkyrja\Orm\Adapter;
use Valkyrja\Orm\BaseQueryBuilder as Contract;
use Valkyrja\Orm\Entity;
use Valkyrja\Orm\Query;

use function assert;

/**
 * Abstract Class SqlBaseQueryBuilder.
 *
 * @author Melech Mizrachi
 */
abstract class SqlBaseQueryBuilder implements Contract
{
    /**
     * The adapter.
     */
    protected Adapter $adapter;

    /**
     * The table upon which the statement executes.
     */
    protected string $table;

    /**
     * The entity to query with.
     *
     * @var class-string<Entity>|null
     */
    protected ?string $entity = null;

    /**
     * SqlBaseQueryBuilder constructor.
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @inheritDoc
     */
    public function table(string $table, string $alias = null): static
    {
        $this->table = $table . ' ' . ((string) $alias);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function entity(string $entity, string $alias = null): static
    {
        assert(is_a($entity, Entity::class, true));

        $this->entity = $entity;

        $this->table($entity, $alias);

        return $this;
    }

    /**
     * @inheritDoc
     */
    abstract public function getQueryString(): string;

    /**
     * @inheritDoc
     */
    public function createQuery(): Query
    {
        return $this->adapter->createQuery($this->getQueryString(), $this->entity);
    }
}
