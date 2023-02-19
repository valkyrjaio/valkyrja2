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
 * Interface View.
 *
 * @author Melech Mizrachi
 */
interface View
{
    /**
     * Make a new View.
     *
     * @param string               $name      [optional] The template to set
     * @param array<string, mixed> $variables [optional] The variables to set
     * @param string|null          $engine    [optional] The engine to use
     */
    public function createTemplate(string $name, array $variables = [], string $engine = null): Template;

    /**
     * Get a render engine.
     *
     * @param string|null $name The name of the engine
     */
    public function getEngine(string $name = null): Engine;

    /**
     * Render a template.
     *
     * @param string               $name      The name of the template to render
     * @param array<string, mixed> $variables [optional] The variables to set
     */
    public function render(string $name, array $variables = []): string;
}
