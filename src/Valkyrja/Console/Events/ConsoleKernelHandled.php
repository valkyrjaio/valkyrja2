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

namespace Valkyrja\Console\Events;

use Valkyrja\Console\Input;

/**
 * Class ConsoleKernelHandled.
 *
 * @author Melech Mizrachi
 */
class ConsoleKernelHandled
{
    /**
     * The input request.
     */
    public Input $input;

    /**
     * The exit code.
     */
    public int $exitCode;

    /**
     * ConsoleKernelHandled constructor.
     */
    public function __construct(Input $input, int $exitCode)
    {
        $this->input    = $input;
        $this->exitCode = $exitCode;
    }
}
