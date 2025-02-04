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

namespace Valkyrja\Orm\Repository\Contract;

use Valkyrja\Orm\Entity\Contract\Entity;

/**
 * Interface CacheRepository.
 *
 * @author Melech Mizrachi
 *
 * @template Entity of Entity
 *
 * @extends Repository<Entity>
 */
interface CacheRepository extends Repository
{
}
