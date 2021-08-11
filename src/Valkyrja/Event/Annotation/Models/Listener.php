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

namespace Valkyrja\Event\Annotation\Models;

use Valkyrja\Annotation\Models\Annotatable;
use Valkyrja\Event\Annotation\Listener as Contract;

/**
 * Class Listener.
 *
 * @author Melech Mizrachi
 */
class Listener extends \Valkyrja\Event\Models\Listener implements Contract
{
    use Annotatable;
}
