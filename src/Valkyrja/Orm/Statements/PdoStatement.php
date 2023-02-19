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

namespace Valkyrja\Orm\Statements;

use PDO;
use PDOStatement as Statement;
use Valkyrja\Orm\PdoStatement as Contract;

use function is_bool;
use function is_int;

/**
 * Class PDOStatement.
 *
 * @author Melech Mizrachi
 */
class PdoStatement implements Contract
{
    /**
     * The PDO Statement.
     */
    protected Statement $statement;

    /**
     * PDOStatement constructor.
     */
    public function __construct(Statement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * @inheritDoc
     */
    public function bindValue(string $parameter, $value): bool
    {
        return $this->statement->bindValue(
            $parameter,
            $value,
            $this->getBindValueType($value)
        );
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
    public function getColumnMeta(int $columnNumber): array
    {
        return $this->statement->getColumnMeta($columnNumber);
    }

    /**
     * @inheritDoc
     */
    public function fetch(): array
    {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn(int $columnNumber = 0): mixed
    {
        return $this->statement->fetchColumn($columnNumber);
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(): array
    {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @inheritDoc
     */
    public function fetchObject(string $className = 'stdClass'): object
    {
        return $this->statement->fetchObject($className);
    }

    /**
     * @inheritDoc
     */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function columnCount(): int
    {
        return $this->statement->columnCount();
    }

    /**
     * @inheritDoc
     */
    public function errorCode(): string
    {
        return $this->statement->errorInfo()[0] ?? '00000';
    }

    /**
     * @inheritDoc
     */
    public function errorMessage(): ?string
    {
        return $this->statement->errorInfo()[2] ?? null;
    }

    /**
     * Get value type to bind with.
     */
    protected function getBindValueType(mixed $value): int
    {
        $type = PDO::PARAM_STR;

        if (is_int($value)) {
            $type = PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            $type = PDO::PARAM_BOOL;
        } elseif ($value === null) {
            $type = PDO::PARAM_NULL;
        }

        return $type;
    }
}
