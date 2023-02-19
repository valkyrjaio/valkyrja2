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

namespace Valkyrja\Annotation;

use Valkyrja\Dispatcher\Dispatch;

/**
 * Interface Annotation.
 *
 * @author Melech Mizrachi
 */
interface Annotation extends Dispatch
{
    /**
     * Get the type.
     */
    public function getType(): ?string;

    /**
     * Set the type.
     *
     * @param string|null $type The type
     */
    public function setType(string $type = null): static;
}
