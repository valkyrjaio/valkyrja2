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

namespace Valkyrja\Http\Responses;

use InvalidArgumentException;
use Valkyrja\Http\Constants\Header;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\Constants\StreamType;
use Valkyrja\Http\Cookie;
use Valkyrja\Http\Exceptions\InvalidStatusCode;
use Valkyrja\Http\Exceptions\InvalidStream;
use Valkyrja\Http\Messages\MessageTrait;
use Valkyrja\Http\Response as Contract;
use Valkyrja\Http\Stream;
use Valkyrja\Http\Streams\Stream as HttpStream;

use function header;
use function sprintf;

/**
 * Class Response.
 *
 * @author Melech Mizrachi
 */
class Response implements Contract
{
    use MessageTrait;

    /**
     * The status phrase.
     */
    protected string $statusPhrase;

    /**
     * NativeResponse constructor.
     *
     * @param Stream $body       [optional] The body
     * @param int    $statusCode [optional] The status
     * @param array  $headers    [optional] The headers
     *
     * @throws InvalidArgumentException
     * @throws InvalidStatusCode
     * @throws InvalidStream
     */
    public function __construct(
        Stream $body = new HttpStream(StreamType::INPUT, 'rw'),
        protected int $statusCode = StatusCode::OK,
        array $headers = []
    ) {
        $this->statusCode   = $this->validateStatusCode($statusCode);
        $this->statusPhrase = StatusCode::TEXTS[$this->statusCode];

        $this->setBody($body);
        $this->setHeaders($headers);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function withStatus(int $code, string $reasonPhrase = null): static
    {
        $new = clone $this;

        $new->statusCode   = $new->validateStatusCode($code);
        $new->statusPhrase = $reasonPhrase ?? StatusCode::TEXTS[$this->statusCode];

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase(): string
    {
        return $this->statusPhrase ?: StatusCode::TEXTS[$this->statusCode];
    }

    /**
     * @inheritDoc
     */
    public function withCookie(Cookie $cookie): static
    {
        return $this->withAddedHeader(Header::SET_COOKIE, (string) $cookie);
    }

    /**
     * @inheritDoc
     */
    public function withoutCookie(Cookie $cookie): static
    {
        $cookie->setValue();
        $cookie->setExpire(0);

        return $this->withAddedHeader(Header::SET_COOKIE, (string) $cookie);
    }

    /**
     * @inheritDoc
     */
    public function sendHttpLine(): static
    {
        $httpLine = sprintf(
            'HTTP/%s %s %s',
            $this->protocol,
            $this->statusCode,
            $this->statusPhrase
        );

        header($httpLine, true, $this->statusCode);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sendHeaders(): static
    {
        foreach ($this->headers as $name => $values) {
            /** @var array $values */
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sendBody(): static
    {
        $stream = $this->stream;

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        echo $stream->getContents();

        ob_flush();
        flush();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function send(): static
    {
        $this->sendHttpLine();
        $this->sendHeaders();
        $this->sendBody();

        return $this;
    }

    /**
     * Validate a status code.
     *
     * @param int $code The code
     *
     * @throws InvalidStatusCode
     */
    protected function validateStatusCode(int $code): int
    {
        if (! StatusCode::isValid($code)) {
            throw new InvalidStatusCode(
                sprintf(
                    'Invalid status code "%d"; must adhere to values set in the %s enum class.',
                    $code,
                    StatusCode::class
                )
            );
        }

        return $code;
    }
}
