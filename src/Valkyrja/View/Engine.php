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

namespace Valkyrja\View;

/**
 * Interface Engine.
 *
 * @author Melech Mizrachi
 */
interface Engine
{
    /**
     * Start rendering.
     */
    public function startRender(): void;

    /**
     * End rendering.
     */
    public function endRender(): string;

    /**
     * Render a file.
     *
     * @param string $name      The file name
     * @param array  $variables [optional] The variables
     */
    public function renderFile(string $name, array $variables = []): string;
}
