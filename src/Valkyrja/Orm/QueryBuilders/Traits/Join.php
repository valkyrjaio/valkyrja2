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

namespace Valkyrja\Orm\QueryBuilders\Traits;

use Valkyrja\Orm\Constants\JoinType;
use Valkyrja\Orm\Constants\Operator;
use Valkyrja\Orm\Constants\Statement;

use function implode;

/**
 * Class Join.
 *
 * @author Melech Mizrachi
 */
trait Join
{
    /**
     * Joins for the query statement.
     */
    protected array $joins = [];

    /**
     * @inheritDoc
     */
    public function join(
        string $table,
        string $column1,
        string $column2,
        string $operator = null,
        string $type = null,
        bool $isWhere = null
    ): static {
        // The operator defaulting to =
        $operator ??= Operator::EQUALS;
        // WHERE or ON for the join
        $statementType = $isWhere ? Statement::WHERE : Statement::ON;
        // Get the type defaulting to inner
        $type ??= JoinType::INNER;
        // Get the join wording
        $join = Statement::JOIN;

        $this->joins[] = "$type $join $table $statementType $column1 $operator $column2";

        return $this;
    }

    /**
     * Get the JOINs of a query statement.
     */
    protected function getJoinQuery(): string
    {
        return empty($this->joins)
            ? ''
            : ' ' . implode(' ', $this->joins);
    }
}
