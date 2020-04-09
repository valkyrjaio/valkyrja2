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

namespace Valkyrja\Api\Enums;

/**
 * Enum Status.
 *
 * @author Melech Mizrachi
 */
final class Status extends \Valkyrja\Support\Enum\Enum
{
    public const SUCCESS = 'success';
    public const ERROR   = 'error';
    public const FAIL    = 'fail';
}
