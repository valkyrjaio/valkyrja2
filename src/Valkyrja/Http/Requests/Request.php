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

use Valkyrja\Http\Constants\Header;
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Http\Constants\StreamType;
use Valkyrja\Http\Exceptions\InvalidArgumentException;
use Valkyrja\Http\Request as Contract;
use Valkyrja\Http\Stream;
use Valkyrja\Http\Streams\Stream as HttpStream;
use Valkyrja\Http\UploadedFile;
use Valkyrja\Http\Uri;
use Valkyrja\Http\Uris\Uri as HttpUri;

/**
 * Class Request.
 *
 * @author Melech Mizrachi
 */
class Request extends SimpleRequest implements Contract
{
    /**
     * The attributes.
     */
    protected array $attributes = [];

    /**
     * The files.
     */
    protected array $files = [];

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
     */
    public function __construct(
        Uri $uri = new HttpUri(),
        string $method = RequestMethod::GET,
        Stream $body = new HttpStream(StreamType::INPUT),
        array $headers = [],
        protected array $server = [],
        protected array $cookies = [],
        protected array $query = [],
        protected array $parsedBody = [],
        protected string $protocol = '1.1',
        UploadedFile ...$files
    ) {
        parent::__construct($uri, $method, $body, $headers);

        $this->files = $files;
    }

    /**
     * @inheritDoc
     */
    public function getServerParams(): array
    {
        return $this->server;
    }

    /**
     * @inheritDoc
     */
    public function getServerParam(string $name, mixed $default = null): mixed
    {
        return $this->server[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function hasServerParam(string $name): bool
    {
        return isset($this->server[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function withCookieParams(array $cookies): static
    {
        $new = clone $this;

        $new->cookies = $cookies;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getCookieParam(string $name, string $default = null): ?string
    {
        return $this->cookies[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function hasCookieParam(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams(): array
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function onlyQueryParams(array $names): array
    {
        return $this->onlyParams($this->query, $names);
    }

    /**
     * @inheritDoc
     */
    public function exceptQueryParams(array $names): array
    {
        return $this->exceptParams($this->query, $names);
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams(array $query): static
    {
        $new = clone $this;

        $new->query = $query;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getQueryParam(string $name, mixed $default = null): mixed
    {
        return $this->query[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function hasQueryParam(string $name): bool
    {
        return isset($this->query[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getUploadedFiles(): array
    {
        return $this->files;
    }

    /**
     * @inheritDoc
     */
    public function withUploadedFiles(UploadedFile ...$uploadedFiles): static
    {
        $new = clone $this;

        $new->files = $uploadedFiles;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getParsedBody(): array
    {
        return $this->parsedBody;
    }

    /**
     * @inheritDoc
     */
    public function onlyParsedBody(array $names): array
    {
        return $this->onlyParams($this->parsedBody, $names);
    }

    /**
     * @inheritDoc
     */
    public function exceptParsedBody(array $names): array
    {
        return $this->exceptParams($this->parsedBody, $names);
    }

    /**
     * @inheritDoc
     */
    public function withParsedBody(array $data): static
    {
        $new = clone $this;

        $new->parsedBody = $data;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getParsedBodyParam(string $name, mixed $default = null): mixed
    {
        return $this->parsedBody[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function hasParsedBodyParam(string $name): bool
    {
        return isset($this->parsedBody[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function onlyAttributes(array $names): array
    {
        return $this->onlyParams($this->attributes, $names);
    }

    /**
     * @inheritDoc
     */
    public function exceptAttributes(array $names): array
    {
        return $this->exceptParams($this->attributes, $names);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function withAttribute(string $name, mixed $value): static
    {
        $new = clone $this;

        $new->attributes[$name] = $value;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute(string $name): static
    {
        $new = clone $this;

        unset($new->attributes[$name]);

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function isXmlHttpRequest(): bool
    {
        return $this->getHeaderLine(Header::X_REQUESTED_WITH) === 'XMLHttpRequest';
    }

    /**
     * Retrieve only the specified params.
     *
     * @param array    $params The params to sort through
     * @param string[] $names  The query param names to retrieve
     */
    protected function onlyParams(array $params, array $names): array
    {
        $onlyParams = [];

        foreach ($names as $name) {
            if (isset($params[$name])) {
                $onlyParams[$name] = $params[$name];
            }
        }

        return $onlyParams;
    }

    /**
     * Retrieve all params except the ones specified.
     *
     * @param array    $params The params to sort through
     * @param string[] $names  The query param names to not retrieve
     */
    protected function exceptParams(array $params, array $names): array
    {
        foreach ($names as $name) {
            if (isset($params[$name])) {
                unset($params[$name]);
            }
        }

        return $params;
    }
}
