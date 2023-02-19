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
use Valkyrja\Orm\Entity;

/**
 * Abstract Class EntityPolicy.
 *
 * @author Melech Mizrachi
 */
abstract class EntityPolicy extends Policy implements Contract
{
    /**
     * The entity class name.
     */
    protected static string $entityClassName;

    /**
     * The entity.
     */
    protected Entity $entity;

    /**
     * Policy constructor.
     *
     * @param Repository $repository The repository
     * @param Entity     $entity     The entity
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
}
