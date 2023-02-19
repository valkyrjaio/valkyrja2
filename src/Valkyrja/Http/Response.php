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

use InvalidArgumentException;

/**
 * Representation of an outgoing, server-side response.
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
interface Response extends Message
{
    /**
     * Gets the response status code.
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int;

    /**
     * Return an instance with the specified status code and, optionally,
     * reason phrase.
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param int         $code         The 3-digit integer result code to set.
     * @param string|null $reasonPhrase The reason phrase to use with the
     *                                  provided status code; if none is provided,
     *                                  implementations MAY use the defaults as
     *                                  suggested in the HTTP specification.
     *
     * @throws InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus(int $code, string $reasonPhrase = null): static;

    /**
     * Gets the response reason phrase associated with the status code.
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or
     * those listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @return string Reason phrase; must return an empty string if none
     *                present.
     */
    public function getReasonPhrase(): string;

    /**
     * Return an instance with the specified cookie appended with the given
     * value.
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the cookie header
     * did not exist previously, it will be added.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new cookie header and/or value.
     *
     * @param Cookie $cookie The cookie model
     *
     * @throws InvalidArgumentException for invalid header names or values.
     */
    public function withCookie(Cookie $cookie): static;

    /**
     * Return an instance with the specified cookie appended to the
     * Set-Cookie header as expired.
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the cookie header
     * did not exist previously, it will be added.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new cookie header and/or value.
     *
     * @param Cookie $cookie The cookie model
     *
     * @throws InvalidArgumentException for invalid header names or values.
     */
    public function withoutCookie(Cookie $cookie): static;

    /**
     * Send the response HTTP line header.
     */
    public function sendHttpLine(): static;

    /**
     * Send the response headers.
     */
    public function sendHeaders(): static;

    /**
     * Send the response body.
     */
    public function sendBody(): static;

    /**
     * Send the response.
     */
    public function send(): static;
}
