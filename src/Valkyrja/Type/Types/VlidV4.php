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

namespace Valkyrja\Type\Types;

use Valkyrja\Type\Support\VlidV4 as Helper;
use Valkyrja\Type\VlidV4 as Contract;

/**
 * Class VlidV4.
 *
 * @author Melech Mizrachi
 */
class VlidV4 extends Vlid implements Contract
{
    public function __construct(string $subject = null)
    {
        if ($subject !== null) {
            Helper::validate($subject);
        }

        parent::__construct($subject ?? Helper::generate());
    }
}
