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
 * Trait DatedFields.
 *
 * @author Melech Mizrachi
 */
trait DatedFields
{
    use Dateable;

    /**
     * The date created date.
     */
    public string $date_created;

    /**
     * The date modified date.
     */
    public string $date_modified;
}
