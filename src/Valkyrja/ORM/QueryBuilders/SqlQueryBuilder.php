<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\ORM\QueryBuilders;

use Valkyrja\ORM\Connection;
use Valkyrja\ORM\Entity;
use Valkyrja\ORM\Enums\OrderBy;
use Valkyrja\ORM\Enums\Statement;
use Valkyrja\ORM\Query;
use Valkyrja\ORM\QueryBuilder;

/**
 * Class SqlQueryBuilder.
 *
 * @author Melech Mizrachi
 */
class SqlQueryBuilder implements QueryBuilder
{
    /**
     * The connection.
     *
     * @var Connection
     */
    protected Connection $connection;

    /**
     * The type of statement to build.
     *
     * @var string
     */
    protected string $type;

    /**
     * The columns for use in a select statement.
     *
     * @var array
     */
    protected array $columns = [];

    /**
     * The table upon which the statement executes.
     *
     * @var string
     */
    protected string $table;

    /**
     * Where conditions for the query statement.
     *
     * @var array
     */
    protected array $where = [];

    /**
     * Values to use for update/insert statements.
     *
     * @var array
     */
    protected array $values = [];

    /**
     * Order by conditions for the query statement.
     *
     * @var array
     */
    protected array $orderBy = [];

    /**
     * Group by conditions for the query statement.
     *
     * @var array
     */
    protected array $groupBy = [];

    /**
     * Limit condition for the query statement.
     *
     * @var int|null
     */
    protected ?int $limit = null;

    /**
     * Offset condition for the query statement.
     *
     * @var int|null
     */
    protected ?int $offset = null;

    /**
     * The built query.
     *
     * @var string|null
     */
    protected ?string $queryString = null;

    /**
     * The entity to query with.
     *
     * @var Entity|string|null
     */
    protected ?string $entity = null;

    /**
     * SqlQueryBuilder constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Create a SELECT query statement.
     * <code>
     *      $queryBuilder->select();
     *      $queryBuilder->select(
     *          [
     *              'column1',
     *              'column2',
     *              ...
     *          ]
     *      );
     * </code>.
     *
     * @param array|null $columns
     *
     * @return static
     */
    public function select(array $columns = null): QueryBuilder
    {
        $this->type = Statement::SELECT;

        if (null !== $columns) {
            $this->columns = $columns;
        } else {
            $this->columns[] = '*';
        }

        return $this;
    }

    /**
     * Create an INSERT query statement.
     * <code>
     *      $queryBuilder->insert();
     * </code>.
     *
     * @return static
     */
    public function insert(): QueryBuilder
    {
        $this->type = Statement::INSERT;

        return $this;
    }

    /**
     * Create an UPDATE query statement.
     * <code>
     *      $queryBuilder->update();
     * </code>.
     *
     * @return static
     */
    public function update(): QueryBuilder
    {
        $this->type = Statement::UPDATE;

        return $this;
    }

    /**
     * Create an DELETE query statement.
     * <code>
     *      $queryBuilder->delete();
     * </code>.
     *
     * @return static
     */
    public function delete(): QueryBuilder
    {
        $this->type = Statement::DELETE;

        return $this;
    }

    /**
     * Set the table on which to perform the query statement.
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table');
     *      $queryBuilder
     *          ->select()
     *          ->table('table', 't');
     * </code>.
     *
     * @param string      $table
     * @param string|null $alias
     *
     * @return static
     */
    public function table(string $table, string $alias = null): QueryBuilder
    {
        $this->table = $table . ' ' . ((string) $alias);

        return $this;
    }

    /**
     * Set the entity to query with.
     * <code>
     *      $queryBuilder
     *          ->entity(Entity::class);
     * </code>.
     *
     * @param string      $entity
     * @param string|null $alias
     *
     * @return static
     */
    public function entity(string $entity, string $alias = null): self
    {
        $this->entity = $entity;

        $this->table(':' . $entity, $alias);

        return $this;
    }

    /**
     * Add a value for a column to set.
     * <code>
     *      $queryBuilder
     *          ->insert()
     *          ->table('table')
     *          ->set('column', ':column');
     *      $queryBuilder
     *          ->update()
     *          ->table('table')
     *          ->set('column', ':column');
     * </code>.
     *
     * @param string $column
     * @param string $value
     *
     * @return static
     */
    public function set(string $column, string $value): QueryBuilder
    {
        $this->values[$column] = $value;

        return $this;
    }

    /**
     * Add a where condition to the query statement.
     * - Each additional use will add an `AND` where condition.
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column = :column');
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column = :column')
     *          ->where('column2 = :column2');
     * </code>.
     *
     * @param string $where
     *
     * @return static
     */
    public function where(string $where): QueryBuilder
    {
        empty($this->where)
            ? $this->setWhere($where)
            : $this->setWhere($where, Statement::WHERE_AND);

        return $this;
    }

    /**
     * Add an additional `AND` where condition to the query statement.
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column = :column')
     *          ->andWhere('column2 = :column2');
     * </code>.
     *
     * @param string $where
     *
     * @return static
     */
    public function andWhere(string $where): QueryBuilder
    {
        empty($this->where)
            ? $this->setWhere($where)
            : $this->setWhere($where, Statement::WHERE_AND);

        return $this;
    }

    /**
     * Add an additional `OR` where condition to the query statement.
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column = :column')
     *          ->andWhere('column2 = :column2');
     * </code>.
     *
     * @param string $where
     *
     * @return static
     */
    public function orWhere(string $where): QueryBuilder
    {
        empty($this->where)
            ? $this->setWhere($where)
            : $this->setWhere($where, Statement::WHERE_OR);

        return $this;
    }

    /**
     * Add an order by without specifying the order to the query statement.
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column = :column')
     *          ->orderBy('column');
     * </code>.
     *
     * @param string      $column
     * @param string|null $type [optional]
     *
     * @return static
     */
    public function orderBy(string $column, string $type = null): QueryBuilder
    {
        $this->setOrderBy($column);

        return $this;
    }

    /**
     * Add an order by ascending to the query statement.
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column = :column')
     *          ->orderByAsc('column');
     * </code>.
     *
     * @param string $column
     *
     * @return static
     */
    public function orderByAsc(string $column): QueryBuilder
    {
        $this->setOrderBy($column, OrderBy::ASC);

        return $this;
    }

    /**
     * Add an order by descending to the query statement.
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column = :column')
     *          ->orderByDesc('column');
     * </code>.
     *
     * @param string $column
     *
     * @return static
     */
    public function orderByDesc(string $column): QueryBuilder
    {
        $this->setOrderBy($column, OrderBy::DESC);

        return $this;
    }

    /**
     * Add limit to the query statement.
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column = :column')
     *          ->limit(1);
     * </code>.
     *
     * @param int $limit
     *
     * @return static
     */
    public function limit(int $limit): QueryBuilder
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Add offset to the query statement.
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column = :column')
     *          ->offset(1);
     * </code>.
     *
     * @param int $offset
     *
     * @return static
     */
    public function offset(int $offset): QueryBuilder
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get the built query string.
     *
     * @return string
     */
    public function getQueryString(): string
    {
        if (null !== $this->queryString) {
            return $this->queryString;
        }

        switch ($this->type) {
            case Statement::SELECT:
                $this->queryString = $this->getSelectQuery();

                break;
            case Statement::UPDATE:
                $this->queryString = $this->getUpdateQuery();

                break;
            case Statement::INSERT:
                $this->queryString = $this->getInsertQuery();

                break;
            case Statement::DELETE:
                $this->queryString = $this->getDeleteQuery();

                break;
        }

        return (string) $this->queryString;
    }

    /**
     * Create a new query.
     *
     * @return Query
     */
    public function createQuery(): Query
    {
        return $this->connection->createQuery($this->getQueryString(), $this->entity);
    }

    /**
     * Set a where condition.
     *
     * @param string      $where
     * @param string|null $type
     *
     * @return void
     */
    protected function setWhere(string $where, string $type = null): void
    {
        $this->where[] = ((string) $type) . ' ' . $where;
    }

    /**
     * Set an order by condition.
     *
     * @param string      $column
     * @param string|null $order
     *
     * @return void
     */
    protected function setOrderBy(string $column, string $order = null): void
    {
        $this->orderBy[] = $column . ' ' . ((string) $order);
    }

    /**
     * Get a SELECT query.
     *
     * @return string
     */
    protected function getSelectQuery(): string
    {
        return $this->type
            . ' ' . implode(', ', $this->columns)
            . ' ' . Statement::FROM
            . ' ' . $this->table
            . ' ' . $this->getWhereQuery()
            . ' ' . $this->getOrderByQuery()
            . ' ' . $this->getLimitQuery()
            . ' ' . $this->getOffsetQuery();
    }

    /**
     * Get an INSERT query.
     *
     * @return string
     */
    protected function getInsertQuery(): string
    {
        return $this->type
            . ' ' . Statement::INTO
            . ' ' . $this->table
            . ' (' . implode(', ', array_keys($this->values)) . ')'
            . ' ' . Statement::VALUE
            . ' (' . implode(', ', $this->values) . ')';
    }

    /**
     * Get an UPDATE query.
     *
     * @return string
     */
    protected function getUpdateQuery(): string
    {
        return $this->type
            . ' ' . $this->table
            . ' ' . $this->getSetQuery()
            . ' ' . $this->getWhereQuery()
            . ' ' . $this->getOrderByQuery()
            . ' ' . $this->getLimitQuery();
    }

    /**
     * Get an DELETE query.
     *
     * @return string
     */
    protected function getDeleteQuery(): string
    {
        return $this->type
            . ' ' . Statement::FROM
            . ' ' . $this->table
            . ' ' . $this->getWhereQuery()
            . ' ' . $this->getOrderByQuery()
            . ' ' . $this->getLimitQuery();
    }

    /**
     * Get the SET part of an INSERT query.
     *
     * @return string
     */
    protected function getSetQuery(): string
    {
        $query = '';

        foreach ($this->values as $column => $value) {
            if (! empty($query)) {
                $query .= ', ';
            }

            $query .= $column . ' = ' . $value;
        }

        return Statement::SET . ' ' . $query;
    }

    /**
     * Get the WHERE part of a query statement.
     *
     * @return string
     */
    protected function getWhereQuery(): string
    {
        return empty($this->where)
            ? ''
            : Statement::WHERE . ' ' . implode(' ', $this->where);
    }

    /**
     * Get the ORDER BY part of a query statement.
     *
     * @return string
     */
    protected function getOrderByQuery(): string
    {
        return empty($this->orderBy)
            ? ''
            : Statement::ORDER_BY . ' ' . implode(', ', $this->orderBy);
    }

    /**
     * Get the LIMIT part of a query statement.
     *
     * @return string
     */
    protected function getLimitQuery(): string
    {
        return null === $this->limit
            ? ''
            : Statement::LIMIT . ' ' . $this->limit;
    }

    /**
     * Get the OFFSET part of a query statement.
     *
     * @return string
     */
    protected function getOffsetQuery(): string
    {
        return null === $this->offset
            ? ''
            : Statement::OFFSET . ' ' . $this->offset;
    }
}
