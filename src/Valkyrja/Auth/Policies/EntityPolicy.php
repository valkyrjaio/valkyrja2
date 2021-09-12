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

namespace Valkyrja\Auth\Policies;

use Valkyrja\Auth\EntityPolicy as Contract;
use Valkyrja\Auth\Repository;
use Valkyrja\ORM\Entity;

/**
 * Abstract Class EntityPolicy.
 *
 * @author Melech Mizrachi
 */
abstract class EntityPolicy extends Policy implements Contract
{
    /**
     * The entity class name.
     *
     * @var string
     */
    protected static string $entityClassName;

    /**
     * The entity param number.
     *
     * @var int
     */
    protected static int $entityParamNumber = 0;

    /**
     * The entity.
     *
     * @var Entity
     */
    protected Entity $entity;

    /**
     * Policy constructor.
     *
     * @param Repository $repository The repository
     */
    public function __construct(Repository $repository, Entity $entity)
    {
        parent::__construct($repository);

        $this->entity = $entity;
    }

    /**
     * @inheritDoc
     */
    public static function getEntityClassName(): string
    {
        return static::$entityClassName;
    }

    /**
     * @inheritDoc
     */
    public static function getEntityParamNumber(): int
    {
        return static::$entityParamNumber;
    }
}
