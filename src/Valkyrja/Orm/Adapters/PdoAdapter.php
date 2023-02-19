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

namespace Valkyrja\Orm\Adapters;

use PDO;
use RuntimeException;
use Valkyrja\Orm\Orm;
use Valkyrja\Orm\PdoAdapter as Contract;
use Valkyrja\Orm\Statement;
use Valkyrja\Orm\Statements\PdoStatement;

use function is_bool;

/**
 * Class PDOAdapter.
 *
 * @author Melech Mizrachi
 */
class PdoAdapter extends Adapter implements Contract
{
    /**
     * The pdo service.
     */
    protected PDO $pdo;

    /**
     * PDOAdapter constructor.
     *
     * @param Orm   $orm    The orm
     * @param PDO   $pdo    The PDO
     * @param array $config The config
     */
    public function __construct(Orm $orm, PDO $pdo, array $config)
    {
        $this->pdo = $pdo;

        parent::__construct($orm, $config);
    }

    /**
     * @inheritDoc
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * @inheritDoc
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /**
     * @inheritDoc
     */
    public function ensureTransaction(): void
    {
        if (! $this->inTransaction()) {
            $this->beginTransaction();
        }
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * @inheritDoc
     */
    public function prepare(string $query): Statement
    {
        $statement = $this->pdo->prepare($query);

        if (is_bool($statement)) {
            throw new RuntimeException('Statement preparation has failed.');
        }

        return $this->orm->createStatement($this, PdoStatement::class, [$statement]);
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(string $table = null, string $idField = null): string
    {
        $name = null;

        if ($this->config['config']['driver'] === 'pgsql' && $table && $idField) {
            $name = "{$table}_{$idField}_seq";
        }

        return (string) $this->pdo->lastInsertId($name);
    }
}
