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
 * Interface ContextAwareContainer.
 *
 * @author Melech Mizrachi
 */
interface ContextAwareContainer extends Container
{
    /**
     * Get a container instance with context.
     *
     * @param class-string|string $context The context class or function name
     * @param string|null         $member  [optional] The context method name
     */
    public function withContext(string $context, string $member = null): static;

    /**
     * Get a container instance with no context.
     */
    public function withoutContext(): static;
}
