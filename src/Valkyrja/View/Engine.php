<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
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
     * Make a new engine.
     *
     * @param View $view The view
     *
     * @return static
     */
    public static function make(View $view): self;

    /**
     * Render a template.
     *
     * @param string $path      The path to render
     * @param array  $variables [optional] The variables to set
     *
     * @return string
     */
    public function render(string $path, array $variables = []): string;
}
