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

namespace Valkyrja\Http\Requests;

use JsonException;
use Valkyrja\Http\Constants\ContentType;
use Valkyrja\Http\Constants\Header;
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Http\Constants\StreamType;
use Valkyrja\Http\Exceptions\InvalidArgumentException;
use Valkyrja\Http\JsonRequest as Contract;
use Valkyrja\Http\Stream;
use Valkyrja\Http\Streams\Stream as HttpStream;
use Valkyrja\Http\UploadedFile;
use Valkyrja\Http\Uri;
use Valkyrja\Http\Uris\Uri as HttpUri;
use Valkyrja\Type\Support\Arr;

/**
 * Class JsonRequest.
 *
 * @author Melech Mizrachi
 */
class JsonRequest extends Request implements Contract
{
    /**
     * The parsed json.
     */
    protected array $parsedJson = [];

    /**
     * Request constructor.
     *
     * @param Uri          $uri        [optional] The uri
     * @param string       $method     [optional] The method
     * @param Stream       $body       [optional] The body stream
     * @param array        $headers    [optional] The headers
     * @param array        $server     [optional] The server
     * @param array        $cookies    [optional] The cookies
     * @param array        $query      [optional] The query string
     * @param array        $parsedBody [optional] The parsed body
     * @param string       $protocol   [optional] The protocol version
     * @param UploadedFile ...$files   [optional] The files
     *
     * @throws InvalidArgumentException
     * @throws JsonException
     */
    public function __construct(
        Uri $uri = new HttpUri(),
        string $method = RequestMethod::GET,
        Stream $body = new HttpStream(StreamType::INPUT),
        array $headers = [],
        array $server = [],
        array $cookies = [],
        array $query = [],
        array $parsedBody = [],
        string $protocol = '1.1',
        UploadedFile ...$files
    ) {
        parent::__construct(
            $uri,
            $method,
            $body,
            $headers,
            $server,
            $cookies,
            $query,
            $parsedBody,
            $protocol,
            ...$files
        );

        if (
            $this->hasHeader(Header::CONTENT_TYPE)
            && str_contains($this->getHeaderLine(Header::CONTENT_TYPE), ContentType::APPLICATION_JSON)
        ) {
            $this->parsedJson = Arr::fromString((string) $body);

            if (! $parsedBody) {
                $this->parsedBody = $this->parsedJson;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getParsedJson(): array
    {
        return $this->parsedJson;
    }

    /**
     * @inheritDoc
     */
    public function onlyParsedJson(array $names): array
    {
        return $this->onlyParams($this->parsedJson, $names);
    }

    /**
     * @inheritDoc
     */
    public function exceptParsedJson(array $names): array
    {
        return $this->exceptParams($this->parsedJson, $names);
    }

    /**
     * @inheritDoc
     */
    public function getParsedJsonParam(string $name, mixed $default = null): mixed
    {
        return $this->parsedJson[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function hasParsedJsonParam(string $name): bool
    {
        return isset($this->parsedJson[$name]);
    }
}
