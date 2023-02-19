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

namespace Valkyrja\Orm\Migrations;

use Exception;
use RuntimeException;

use function explode;
use function file_get_contents;
use function trim;

/**
 * Class SqlFileMigration.
 */
abstract class SqlFileMigration extends TransactionalMigration
{
    /**
     * @inheritDoc
     */
    protected function runMigration(): void
    {
        $this->executeSql($this->getRunMigrationFilePath());
    }

    /**
     * @inheritDoc
     */
    protected function rollbackMigration(): void
    {
        $this->executeSql($this->getRollbackMigrationFilePath());
    }

    /**
     * Execute sql.
     *
     * @param string $sqlFilePath The sql file path
     */
    protected function executeSql(string $sqlFilePath): void
    {
        $sql = file_get_contents($sqlFilePath);

        foreach (explode(';', trim($sql)) as $queryString) {
            if (! $queryString) {
                continue;
            }

            $query = $this->orm->useConnection()->createQuery($queryString);

            if (! $query->execute()) {
                throw new RuntimeException($query->getError());
            }
        }
    }

    /**
     * Do on run failure.
     *
     * @param Exception $exception The exception
     */
    protected function runFailure(Exception $exception): void
    {
    }

    /**
     * Get the run sql file path.
     */
    abstract protected function getRunMigrationFilePath(): string;

    /**
     * Get the rollback sql file path.
     */
    abstract protected function getRollbackMigrationFilePath(): string;
}
