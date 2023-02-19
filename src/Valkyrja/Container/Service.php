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

namespace Valkyrja\Container;

/**
 * Interface Service.
 *
 * @author Melech Mizrachi
 */
interface Service
{
    /**
     * Make a new instance of this service.
     *
     * @param Container $container The container
     * @param array     $arguments [optional] The arguments
     */
    public static function make(Container $container, array $arguments = []): static;
}
