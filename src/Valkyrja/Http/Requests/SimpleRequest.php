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

use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Http\Constants\StreamType;
use Valkyrja\Http\Exceptions\InvalidArgumentException;
use Valkyrja\Http\Exceptions\InvalidMethod;
use Valkyrja\Http\Exceptions\InvalidRequestTarget;
use Valkyrja\Http\Messages\MessageTrait;
use Valkyrja\Http\SimpleRequest as SimpleRequestContract;
use Valkyrja\Http\Stream;
use Valkyrja\Http\Streams\Stream as HttpStream;
use Valkyrja\Http\Uri;
use Valkyrja\Http\Uris\Uri as HttpUri;

use function in_array;
use function preg_match;
use function sprintf;

/**
 * Class SimpleRequest.
 *
 * @author Melech Mizrachi
 */
class SimpleRequest implements SimpleRequestContract
{
    use MessageTrait;

    public static string $HOST_NAME      = 'Host';
    public static string $HOST_NAME_NORM = 'host';

    /**
     * The request target.
     */
    protected ?string $requestTarget = null;

    /**
     * SimpleRequest constructor.
     *
     * @param Uri    $uri     [optional] The uri
     * @param string $method  [optional] The method
     * @param Stream $body    [optional] The body stream
     * @param array  $headers [optional] The headers
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected Uri $uri = new HttpUri(),
        protected string $method = RequestMethod::GET,
        Stream $body = new HttpStream(StreamType::INPUT),
        array $headers = []
    ) {
        $this->setBody($body);
        $this->setHeaders($headers);
        $this->validateMethod($method);
        $this->validateProtocolVersion($this->protocol);

        if ($this->hasHeader(static::$HOST_NAME) && $this->uri->getHost()) {
            $this->headerNames[static::$HOST_NAME_NORM] = static::$HOST_NAME;
            $this->headers[static::$HOST_NAME]          = [
                $this->uri->getHost(),
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();

        if ($this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }

        if (empty($target)) {
            $target = '/';
        }

        return $target;
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget(string $requestTarget): static
    {
        $this->validateRequestTarget($requestTarget);

        $new = clone $this;

        $new->requestTarget = $requestTarget;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod(string $method): static
    {
        $this->validateMethod($method);

        $new = clone $this;

        $new->method = $method;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getUri(): Uri
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function withUri(Uri $uri, bool $preserveHost = false): static
    {
        $new = clone $this;

        $new->uri = $uri;

        if ($preserveHost && $this->hasHeader(static::$HOST_NAME)) {
            return $new;
        }

        if (! $uri->getHost()) {
            return $new;
        }

        $host = $uri->getHost();

        $new->headerNames[static::$HOST_NAME_NORM] = static::$HOST_NAME;

        $new->headers = $this->injectHeader(static::$HOST_NAME, $host, $new->headerNames, true);

        return $new;
    }

    /**
     * Validate a request target.
     *
     * @param string $requestTarget The request target
     *
     * @throws InvalidRequestTarget
     */
    protected function validateRequestTarget(string $requestTarget): void
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidRequestTarget('Invalid request target provided; cannot contain whitespace');
        }
    }

    /**
     * Validate a method.
     *
     * @param string $method The method
     *
     * @throws InvalidMethod
     */
    protected function validateMethod(string $method): void
    {
        if (! in_array($method, RequestMethod::ANY, true)) {
            throw new InvalidMethod(sprintf('Unsupported HTTP method "%s" provided', $method));
        }
    }
}
