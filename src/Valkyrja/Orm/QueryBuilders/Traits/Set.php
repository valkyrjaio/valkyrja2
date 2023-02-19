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

use Valkyrja\Orm\Constants\Statement;

use function implode;

/**
 * Class Set.
 *
 * @author Melech Mizrachi
 */
trait Set
{
    /**
     * Values to use for update/insert statements.
     */
    protected array $values = [];

    /**
     * @inheritDoc
     */
    public function set(string $column, mixed $value = null): static
    {
        $this->values[$column] = $value ?? ":$column";

        return $this;
    }

    /**
     * Get the SET part of an INSERT query.
     */
    protected function getSetQuery(): string
    {
        $values = [];

        foreach ($this->values as $column => $value) {
            $values[] = $column . ' = ' . $value;
        }

        return Statement::SET . ' ' . implode(', ', $values);
    }
}
