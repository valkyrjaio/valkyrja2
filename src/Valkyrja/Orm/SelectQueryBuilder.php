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
 * Interface SelectQueryBuilder.
 *
 * @author Melech Mizrachi
 */
interface SelectQueryBuilder extends BaseQueryBuilder, WhereQueryBuilder
{
    /**
     * Set the columns to return.
     *
     * <code>
     *      $queryBuilder->columns();
     *      $queryBuilder->columns(
     *          [
     *              'column1',
     *              'column2',
     *              ...
     *          ]
     *      );
     * </code>
     *
     * @param array|null $columns [optional] The columns to return. Defaults to *.
     */
    public function columns(array $columns = null): static;

    /**
     * Join with another table.
     *
     * @param string      $table    The table to join on
     * @param string      $column1  The column to join on
     * @param string      $column2  The secondary column to join on
     * @param string|null $operator [optional] The operator
     * @param string|null $type     [optional] The type of join
     * @param bool|null   $isWhere  [optional] Whether this is a where join
     */
    public function join(
        string $table,
        string $column1,
        string $column2,
        string $operator = null,
        string $type = null,
        bool $isWhere = null
    ): static;

    /**
     * Add an groupBy by to the query statement.
     *
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column', '=', ':column')
     *          ->groupBy('column');
     * </code>
     */
    public function groupBy(string $column): static;

    /**
     * Add an order by to the query statement.
     *
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column', '=', ':column')
     *          ->orderBy('column');
     * </code>
     *
     * @param string|null $type [optional]
     */
    public function orderBy(string $column, string $type = null): static;

    /**
     * Add an order by ascending to the query statement.
     *
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column', '=', ':column')
     *          ->orderByAsc('column');
     * </code>
     */
    public function orderByAsc(string $column): static;

    /**
     * Add an order by descending to the query statement.
     *
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column', '=', ':column')
     *          ->orderByDesc('column');
     * </code>
     */
    public function orderByDesc(string $column): static;

    /**
     * Add limit to the query statement.
     *
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column', '=', ':column')
     *          ->limit(1);
     * </code>
     */
    public function limit(int $limit): static;

    /**
     * Add offset to the query statement.
     *
     * <code>
     *      $queryBuilder
     *          ->select()
     *          ->table('table')
     *          ->where('column', '=', ':column')
     *          ->offset(1);
     * </code>
     */
    public function offset(int $offset): static;
}
