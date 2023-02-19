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

/**
 * Abstract Class TransactionalMigration.
 *
 * @author Melech Mizrachi
 */
abstract class TransactionalMigration extends Migration
{
    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function run(): void
    {
        $orm = $this->orm;

        try {
            $orm->ensureTransaction();

            $this->runMigration();

            $orm->persist();
        } catch (Exception $exception) {
            $orm->rollback();

            $this->runFailure($exception);

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function rollback(): void
    {
        $orm = $this->orm;

        try {
            $orm->ensureTransaction();

            $this->rollbackMigration();

            $orm->persist();
        } catch (Exception $exception) {
            $orm->rollback();

            $this->rollbackFailure($exception);

            throw $exception;
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
     * Do on rollback failure.
     *
     * @param Exception $exception The exception
     */
    protected function rollbackFailure(Exception $exception): void
    {
    }

    /**
     * Run the migration.
     */
    abstract protected function runMigration(): void;

    /**
     * Rollback the migration.
     */
    abstract protected function rollbackMigration(): void;
}
