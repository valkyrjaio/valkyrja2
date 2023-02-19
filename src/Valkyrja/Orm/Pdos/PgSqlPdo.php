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

namespace Valkyrja\Orm\Pdos;

use PDO as BasePDO;

/**
 * Class PgSqlPDO.
 *
 * @author Melech Mizrachi
 */
class PgSqlPdo extends Pdo
{
    /**
     * The default options.
     */
    protected static array $defaultOptions = [
        BasePDO::ATTR_PERSISTENT        => true,
        BasePDO::ATTR_CASE              => BasePDO::CASE_NATURAL,
        BasePDO::ATTR_ERRMODE           => BasePDO::ERRMODE_EXCEPTION,
        BasePDO::ATTR_ORACLE_NULLS      => BasePDO::NULL_NATURAL,
        BasePDO::ATTR_STRINGIFY_FETCHES => false,
    ];

    /**
     * PgSqlPDO constructor.
     *
     * @param array $config The config
     */
    public function __construct(array $config)
    {
        $charset = $config['charset'] ?? 'utf8';

        $dsn = $this->getDsnPart($config, 'sslmode')
            . ";options='--client_encoding=$charset'";

        parent::__construct($config, 'pgsql', $dsn);
    }
}
