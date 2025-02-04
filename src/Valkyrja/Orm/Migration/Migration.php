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

namespace Valkyrja\Orm\Migration;

use Valkyrja\Orm\Contract\Orm;
use Valkyrja\Orm\Schema\Contract\Migration as Contract;

/**
 * Abstract Class Migration.
 *
 * @author Melech Mizrachi
 */
abstract class Migration implements Contract
{
    /**
     * The ORM service.
     *
     * @var Orm
     */
    protected Orm $orm;

    /**
     * Migration constructor.
     *
     * @param Orm $orm The ORM service
     */
    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    /**
     * @inheritDoc
     */
    abstract public function run(): void;

    /**
     * @inheritDoc
     */
    abstract public function rollback(): void;
}
