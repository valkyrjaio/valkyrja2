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
 * Interface RedirectResponse.
 *
 * @author Melech Mizrachi
 */
interface RedirectResponse extends Response
{
    /**
     * Get the uri.
     */
    public function getUri(): string;

    /**
     * Set the uri.
     *
     * @param string $uri The uri
     */
    public function setUri(string $uri): static;

    /**
     * Set the redirect uri to secure.
     *
     * @param string  $path    The path
     * @param Request $request The request
     */
    public function secure(string $path, Request $request): static;

    /**
     * Redirect back to the referer.
     *
     * @param Request $request The request
     */
    public function back(Request $request): static;

    /**
     * Throw this redirect.
     */
    public function throw(): void;
}
