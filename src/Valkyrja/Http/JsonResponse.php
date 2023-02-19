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

namespace Valkyrja\Http;

/**
 * Interface JsonResponse.
 *
 * @author Melech Mizrachi
 */
interface JsonResponse extends Response
{
    /**
     * With callback.
     *
     * @param string $callback The callback
     */
    public function withCallback(string $callback): static;

    /**
     * Without callback.
     */
    public function withoutCallback(): static;
}
