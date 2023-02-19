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

namespace Valkyrja\Http\Messages;

use InvalidArgumentException;
use Valkyrja\Http\Exceptions\InvalidProtocolVersion;
use Valkyrja\Http\Stream;
use Valkyrja\Http\Support\HeaderSecurity;

use function array_merge;
use function implode;
use function is_array;
use function preg_match;
use function sprintf;
use function strtolower;

/**
 * Trait MessageTrait.
 *
 * @author Melech Mizrachi
 */
trait MessageTrait
{
    /**
     * The headers with normalized header names.
     *
     * @var string[][]
     */
    protected array $headers = [];

    /**
     * Original header names.
     */
    protected array $headerNames = [];

    /**
     * The protocol.
     */
    protected string $protocol = '1.1';

    /**
     * The stream.
     */
    protected Stream $stream;

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion(string $version): static
    {
        $this->validateProtocolVersion($version);

        $this->protocol = $version;

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return string[][]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    /**
     * @inheritDoc
     *
     * @return string[]
     */
    public function getHeader(string $name): array
    {
        if (! $this->hasHeader($name)) {
            return [];
        }

        $name = $this->headerNames[strtolower($name)];

        return $this->headers[$name];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine(string $name): string
    {
        $value = $this->getHeader($name);

        if (empty($value)) {
            return '';
        }

        return implode(',', $value);
    }

    /**
     * @inheritDoc
     */
    public function withHeader(string $name, string ...$values): static
    {
        HeaderSecurity::assertValidName($name);

        $normalized = strtolower($name);

        $new = clone $this;

        if ($new->hasHeader($name)) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }

        $new->headerNames[$normalized] = $name;

        $new->headers[$name] = $this->assertHeaderValues(...$values);

        return $new;
    }

    /**
     * @inheritDoc
     *
     * @param string ...$value Header value(s).
     */
    public function withAddedHeader(string $name, string ...$values): static
    {
        HeaderSecurity::assertValidName($name);

        if (! $this->hasHeader($name)) {
            return $this->withHeader($name, ...$values);
        }

        $name = $this->headerNames[strtolower($name)];

        $new = clone $this;

        $new->headers[$name] = array_merge($this->headers[$name], $this->assertHeaderValues(...$values));

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader(string $name): static
    {
        if (! $this->hasHeader($name)) {
            return clone $this;
        }

        $normalized = strtolower($name);
        $original   = $this->headerNames[$normalized];
        $new        = clone $this;

        unset($new->headers[$original], $new->headerNames[$normalized]);

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): Stream
    {
        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function withBody(Stream $body): static
    {
        $new = clone $this;

        $new->setBody($body);

        return $new;
    }

    /**
     * Set the body.
     *
     * @param Stream $body The body
     */
    protected function setBody(Stream $body): void
    {
        $this->stream = $body;
    }

    /**
     * Set headers.
     *
     * @param array<string, string|array> $originalHeaders The original headers
     *
     * @throws InvalidArgumentException
     */
    protected function setHeaders(array $originalHeaders): void
    {
        $headerNames = $headers = [];

        foreach ($originalHeaders as $header => $value) {
            $value = is_array($value) ? $value : [$value];

            HeaderSecurity::assertValidName($header);

            $headerNames[strtolower($header)] = $header;

            $headers[$header] = $this->assertHeaderValues(...$value);
        }

        $this->headerNames = $headerNames;
        $this->headers     = $headers;
    }

    /**
     * Validate the protocol version.
     *
     * @param string $version The version
     *
     * @throws InvalidProtocolVersion
     */
    protected function validateProtocolVersion(string $version): void
    {
        // HTTP/1 uses a "<major>.<minor>" numbering scheme to indicate
        // versions of the protocol, while HTTP/2 does not.
        if (! preg_match('#^(1\.[01]|2)$#', $version)) {
            throw new InvalidProtocolVersion(
                sprintf(
                    'Unsupported HTTP protocol version "%s" provided',
                    $version
                )
            );
        }
    }

    /**
     * Filter header values.
     *
     * @param string ...$values Header values
     *
     * @throws InvalidArgumentException
     *
     * @return string[]
     */
    protected function assertHeaderValues(string ...$values): array
    {
        foreach ($values as $value) {
            HeaderSecurity::assertValid($value);
        }

        return $values;
    }

    /**
     * Inject a header in a headers array.
     *
     * @param string     $header   The header to set
     * @param string     $value    The value to set
     * @param array|null $headers  [optional] The headers
     * @param bool       $override [optional] Whether to override any existing value
     */
    protected function injectHeader(string $header, string $value, array $headers = null, bool $override = false): array
    {
        // The headers
        $headers = $headers ?? [];
        // Normalize the content type header
        $normalized = strtolower($header);
        // The original value for the header (if it exists in the headers array)
        // Defaults to the value passed in
        $originalValue = $value;

        // Iterate through all the headers
        foreach ($headers as $headerIndex => $headerValue) {
            // Normalize the header name and check if it matches the normalized
            // passed in header
            if (strtolower($headerIndex) === $normalized) {
                // Set the original value as this header value
                $originalValue = $headerValue;

                // Unset the header as we want to use the header string that was
                // passed in as the header
                unset($headers[$headerIndex]);
            }
        }

        // Set the header in the headers list
        $headers[$header] = [$override ? $originalValue : $value];

        return $headers;
    }
}
