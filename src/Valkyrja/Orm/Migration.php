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
 * Interface Migration.
 *
 * @author Melech Mizrachi
 */
interface Migration
{
    /**
     * Run the migration.
     */
    public function run(): void;

    /**
     * Rollback the migration.
     */
    public function rollback(): void;
}
