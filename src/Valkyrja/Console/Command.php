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

namespace Valkyrja\Console;

use Valkyrja\Dispatcher\Dispatch;

/**
 * Interface Command.
 *
 * @author Melech Mizrachi
 */
interface Command extends Dispatch
{
    /**
     * Get the path.
     */
    public function getPath(): ?string;

    /**
     * Set the path.
     *
     * @param string $path The path
     */
    public function setPath(string $path): static;

    /**
     * Get the regex.
     */
    public function getRegex(): ?string;

    /**
     * Set the regex.
     *
     * @param string|null $regex The regex
     */
    public function setRegex(string $regex = null): static;

    /**
     * Get the params.
     */
    public function getParams(): ?array;

    /**
     * Set the params.
     *
     * @param array|null $params The params
     */
    public function setParams(array $params = null): static;

    /**
     * Get the segments.
     */
    public function getSegments(): ?array;

    /**
     * Set the segments.
     *
     * @param array|null $segments The segments
     */
    public function setSegments(array $segments = null): static;

    /**
     * Get the description.
     */
    public function getDescription(): ?string;

    /**
     * Set the description.
     *
     * @param string|null $description The description
     */
    public function setDescription(string $description = null): static;
}
