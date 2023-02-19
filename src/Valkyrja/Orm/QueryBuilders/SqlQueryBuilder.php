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

use Valkyrja\Orm\Constants\OrderBy;
use Valkyrja\Orm\Constants\Statement;
use Valkyrja\Orm\QueryBuilder;
use Valkyrja\Orm\QueryBuilders\Traits\Join;
use Valkyrja\Orm\QueryBuilders\Traits\Set;
use Valkyrja\Orm\QueryBuilders\Traits\Where;

use function array_keys;
use function implode;

/**
 * Class SqlQueryBuilder.
 *
 * @author Melech Mizrachi
 */
class SqlQueryBuilder extends SqlBaseQueryBuilder implements QueryBuilder
{
    use Join;
    use Set;
    use Where;

    /**
     * The type of statement to build.
     */
    protected string $type;

    /**
     * The columns for use in a select statement.
     */
    protected array $columns = [];

    /**
     * Order by conditions for the query statement.
     */
    protected array $orderBy = [];

    /**
     * Group by conditions for the query statement.
     */
    protected array $groupBy = [];

    /**
     * Limit condition for the query statement.
     */
    protected ?int $limit = null;

    /**
     * Offset condition for the query statement.
     */
    protected ?int $offset = null;

    /**
     * @inheritDoc
     */
    public function select(array $columns = null): static
    {
        $this->type    = Statement::SELECT;
        $this->columns = $columns ?? ['*'];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function insert(): static
    {
        $this->type = Statement::INSERT;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function update(): static
    {
        $this->type = Statement::UPDATE;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): static
    {
        $this->type = Statement::DELETE;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function groupBy(string $column): static
    {
        $this->groupBy[] = $column;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orderBy(string $column, string $type = null): static
    {
        $this->orderBy[] = $column . ' ' . ((string) $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orderByAsc(string $column): static
    {
        return $this->orderBy($column, OrderBy::ASC);
    }

    /**
     * @inheritDoc
     */
    public function orderByDesc(string $column): static
    {
        return $this->orderBy($column, OrderBy::DESC);
    }

    /**
     * @inheritDoc
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQueryString(): string
    {
        $queryString = '';

        switch ($this->type) {
            case Statement::SELECT:
                $queryString = $this->getSelectQuery();

                break;
            case Statement::UPDATE:
                $queryString = $this->getUpdateQuery();

                break;
            case Statement::INSERT:
                $queryString = $this->getInsertQuery();

                break;
            case Statement::DELETE:
                $queryString = $this->getDeleteQuery();

                break;
        }

        return $queryString;
    }

    /**
     * Get a SELECT query.
     */
    protected function getSelectQuery(): string
    {
        return Statement::SELECT
            . ' ' . implode(', ', $this->columns)
            . ' ' . Statement::FROM
            . ' ' . $this->table
            . ' ' . $this->getJoinQuery()
            . ' ' . $this->getWhereQuery()
            . ' ' . $this->getOrderByQuery()
            . ' ' . $this->getLimitQuery()
            . ' ' . $this->getOffsetQuery();
    }

    /**
     * Get an INSERT query.
     */
    protected function getInsertQuery(): string
    {
        return Statement::INSERT
            . ' ' . Statement::INTO
            . ' ' . $this->table
            . ' (' . implode(', ', array_keys($this->values)) . ')'
            . ' ' . Statement::VALUES
            . ' (' . implode(', ', $this->values) . ')'
            . ' ' . $this->getJoinQuery();
    }

    /**
     * Get an UPDATE query.
     */
    protected function getUpdateQuery(): string
    {
        return Statement::UPDATE
            . ' ' . $this->table
            . ' ' . $this->getSetQuery()
            . ' ' . $this->getWhereQuery()
            . ' ' . $this->getJoinQuery();
    }

    /**
     * Get an DELETE query.
     */
    protected function getDeleteQuery(): string
    {
        return Statement::DELETE
            . ' ' . Statement::FROM
            . ' ' . $this->table
            . ' ' . $this->getWhereQuery()
            . ' ' . $this->getJoinQuery();
    }

    /**
     * Get the GROUP BY part of a query statement.
     */
    protected function getGroupByQuery(): string
    {
        return empty($this->orderBy) || $this->isCount()
            ? ''
            : Statement::GROUP_BY . ' ' . implode(', ', $this->groupBy);
    }

    /**
     * Get the ORDER BY part of a query statement.
     */
    protected function getOrderByQuery(): string
    {
        return empty($this->orderBy) || $this->isCount()
            ? ''
            : Statement::ORDER_BY . ' ' . implode(', ', $this->orderBy);
    }

    /**
     * Get the LIMIT part of a query statement.
     */
    protected function getLimitQuery(): string
    {
        return $this->limit === null || $this->isCount()
            ? ''
            : Statement::LIMIT . ' ' . $this->limit;
    }

    /**
     * Get the OFFSET part of a query statement.
     */
    protected function getOffsetQuery(): string
    {
        return $this->offset === null || $this->isCount()
            ? ''
            : Statement::OFFSET . ' ' . $this->offset;
    }

    /**
     * Determine whether this is a count statement.
     */
    protected function isCount(): bool
    {
        return $this->columns[0] === Statement::COUNT_ALL;
    }
}
