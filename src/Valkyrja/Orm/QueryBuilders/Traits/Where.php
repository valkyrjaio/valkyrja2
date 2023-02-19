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

use Valkyrja\Orm\Constants\Operator;
use Valkyrja\Orm\Constants\Statement;
use Valkyrja\Orm\Enums\WhereType;
use Valkyrja\Orm\QueryBuilder;
use Valkyrja\Orm\Support\Helpers;

use function array_keys;
use function implode;
use function is_array;

/**
 * Class Where.
 *
 * @author Melech Mizrachi
 */
trait Where
{
    /**
     * Where conditions for the query statement.
     */
    protected array $where = [];

    /**
     * Has a where group been started.
     */
    protected bool $whereGroupStarted = false;

    /**
     * @inheritDoc
     */
    public function where(string $column, string $operator = null, mixed $value = null, bool $setType = true): static
    {
        $this->setWhere(
            $this->getWhereString($column, $operator ?? Operator::EQUALS, $value),
            $setType ? Statement::WHERE_AND : ''
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function startWhereGroup(): static
    {
        $this->whereGroupStarted = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function endWhereGroup(): static
    {
        $this->where[] = ')';

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function whereType(WhereType $type = WhereType::AND): static
    {
        $this->where[] = $type->value;

        return $this;
    }

    /**
     * Set a where condition.
     */
    protected function setWhere(string $where, string $type): void
    {
        $this->where[] = (empty($this->where) ? '' : $type)
            . ' '
            . ($this->whereGroupStarted ? '(' : '')
            . $where;

        $this->whereGroupStarted = false;
    }

    /**
     * Get a where string.
     *
     * @param mixed $value
     */
    protected function getWhereString(string $column, string $operator, mixed $value = null): string
    {
        return $column . ' ' . $operator . ' ' . $this->getWhereValue($column, $value);
    }

    /**
     * Get a where value.
     */
    protected function getWhereValue(string $column, mixed $value): string
    {
        $columnValueBind = Helpers::getColumnForValueBind($column);

        if ($value instanceof QueryBuilder) {
            return '(' . $value->getQueryString() . ')';
        }

        if (! is_array($value)) {
            return $columnValueBind;
        }

        return '(' . $columnValueBind . implode(', ' . $columnValueBind, array_keys($value)) . ')';
    }

    /**
     * Get the WHERE part of a query statement.
     */
    protected function getWhereQuery(): string
    {
        return empty($this->where)
            ? ''
            : Statement::WHERE . ' ' . implode(' ', $this->where);
    }
}
