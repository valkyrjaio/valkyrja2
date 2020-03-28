<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Auth\Entities;

use Valkyrja\Auth\LockableUser as Contract;
use Valkyrja\ORM\Entities\EntityFields;

/**
 * Entity LockableUser.
 *
 * @author Melech Mizrachi
 */
class LockableUser implements Contract
{
    use EntityFields;
    use UserFields;
    use LockableUserFields;
}
