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

namespace Valkyrja\Orm\Entities;

/**
 * Trait SoftDeleteFields.
 *
 * @author Melech Mizrachi
 */
trait SoftDeleteFields
{
    use SoftDeletable;

    /**
     * The deleted flag.
     */
    public bool $is_deleted = false;

    /**
     * The date deleted date.
     */
    public ?string $date_deleted = null;
}
