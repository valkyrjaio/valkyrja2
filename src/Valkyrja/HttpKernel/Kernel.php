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

namespace Valkyrja\HttpKernel;

use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Routing\MiddlewareAware;

/**
 * Interface Kernel.
 *
 * @author Melech Mizrachi
 */
interface Kernel extends MiddlewareAware
{
    /**
     * Handle a request.
     *
     * @param Request $request The request
     */
    public function handle(Request $request): Response;

    /**
     * Send the response.
     */
    public function send(Response $response): static;

    /**
     * Terminate the kernel request.
     *
     * @param Request  $request  The request
     * @param Response $response The response
     */
    public function terminate(Request $request, Response $response): void;

    /**
     * Run the kernel.
     *
     * @param Request $request The request
     */
    public function run(Request $request): void;
}
