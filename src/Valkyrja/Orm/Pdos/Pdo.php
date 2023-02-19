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
 * Class DefaultPDO.
 *
 * @author Melech Mizrachi
 */
abstract class Pdo extends BasePDO
{
    /**
     * The default options.
     */
    protected static array $defaultOptions = [
        BasePDO::ATTR_CASE              => BasePDO::CASE_NATURAL,
        BasePDO::ATTR_ERRMODE           => BasePDO::ERRMODE_EXCEPTION,
        BasePDO::ATTR_ORACLE_NULLS      => BasePDO::NULL_NATURAL,
        BasePDO::ATTR_STRINGIFY_FETCHES => false,
        BasePDO::ATTR_EMULATE_PREPARES  => false,
    ];

    /**
     * PDO constructor.
     *
     * @param array       $config The config
     * @param string|null $driver [optional] The driver
     * @param string|null $dsn    [optional] The added dsn
     */
    public function __construct(
        array $config,
        string $driver = null,
        string $dsn = null
    ) {
        $config['dsn'] = ($driver ?? 'mysql')
            . ":dbname={$config['db']}"
            . $this->getDsnPart($config, 'host')
            . $this->getDsnPart($config, 'port')
            . $this->getDsnPart($config, 'user')
            . $this->getDsnPart($config, 'password')
            . ($dsn ?? '');

        parent::__construct(
            $config['dsn'],
            null,
            null,
            $config['options'] ?? static::$defaultOptions
        );
    }

    /**
     * Get dsn part.
     *
     * @param array  $config  The config
     * @param string $name    The dsn part name
     * @param mixed  $default [optional] The default value
     */
    protected function getDsnPart(array $config, string $name, mixed $default = null): string
    {
        $value = $config[$name] ?? $default;

        return $value ? ";$name=$value" : '';
    }
}
