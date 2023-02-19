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
 * Class JsonRequest.
 *
 * @author Melech Mizrachi
 */
interface JsonRequest extends Request
{
    /**
     * Retrieve any parameters provided in the request body.
     * If the request Content-Type is either application/json
     * and the request method is POST, this method MUST
     * return the decoded contents of the body.
     *
     * @return array The decoded json, if any.
     *               These will typically be an array or object.
     */
    public function getParsedJson(): array;

    /**
     * Retrieve only the specified request body params.
     *
     * @param string[] $names The param names to retrieve
     */
    public function onlyParsedJson(array $names): array;

    /**
     * Retrieve all request body params except the ones specified.
     *
     * @param string[] $names The param names to not retrieve
     */
    public function exceptParsedJson(array $names): array;

    /**
     * Retrieve a specific json param value.
     * Retrieves a json param value sent by the client to the server.
     *
     * @param string     $name    The json param name to retrieve
     * @param mixed|null $default [optional] Default value to return if the param does not exist
     */
    public function getParsedJsonParam(string $name, mixed $default = null): mixed;

    /**
     * Determine if a specific json param exists.
     *
     * @param string $name The json param name to check for
     */
    public function hasParsedJsonParam(string $name): bool;
}
