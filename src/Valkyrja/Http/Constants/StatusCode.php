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

namespace Valkyrja\Http\Constants;

/**
 * Status code constants.
 *
 * @author Melech Mizrachi
 *
 * @see   http://www.iana.org/assignments/http-status-codes/
 * - Hypertext Transfer Protocol (HTTP) Status Code Registry
 */
final class StatusCode
{
    public const MIN                             = 100;
    public const MAX                             = 599;

    public const CONTINUE                        = 100;
    public const SWITCHING_PROTOCOLS             = 101;
    public const PROCESSING                      = 102;
    public const OK                              = 200;
    public const CREATED                         = 201;
    public const ACCEPTED                        = 202;
    public const NON_AUTHORITATIVE_INFORMATION   = 203;
    public const NO_CONTENT                      = 204;
    public const RESET_CONTENT                   = 205;
    public const PARTIAL_CONTENT                 = 206;
    public const MULTI_STATUS                    = 207;
    public const ALREADY_REPORTED                = 208;
    public const IM_USED                         = 226;
    public const MULTIPLE_CHOICES                = 300;
    public const MOVED_PERMANENTLY               = 301;
    public const FOUND                           = 302;
    public const SEE_OTHER                       = 303;
    public const NOT_MODIFIED                    = 304;
    public const USE_PROXY                       = 305;
    public const TEMPORARY_REDIRECT              = 307;
    public const PERMANENT_REDIRECT              = 308;
    public const BAD_REQUEST                     = 400;
    public const UNAUTHORIZED                    = 401;
    public const PAYMENT_REQUIRED                = 402;
    public const FORBIDDEN                       = 403;
    public const NOT_FOUND                       = 404;
    public const METHOD_NOT_ALLOWED              = 405;
    public const NOT_ACCEPTABLE                  = 406;
    public const PROXY_AUTHENTICATION_REQUIRED   = 407;
    public const REQUEST_TIMEOUT                 = 408;
    public const CONFLICT                        = 409;
    public const GONE                            = 410;
    public const LENGTH_REQUIRED                 = 411;
    public const PRECONDITION_FAILED             = 412;
    public const PAYLOAD_TOO_LARGE               = 413;
    public const URI_TOO_LONG                    = 414;
    public const UNSUPPORTED_MEDIA_TYPE          = 415;
    public const RANGE_NOT_SATISFIABLE           = 416;
    public const EXPECTATION_FAILED              = 417;
    public const I_AM_A_TEAPOT                   = 418;
    public const MISDIRECTED_REQUEST             = 421;
    public const UNPROCESSABLE_ENTITY            = 422;
    public const LOCKED                          = 423;
    public const FAILED_DEPENDENCY               = 424;
    public const UPGRADE_REQUIRED                = 426;
    public const PRECONDITION_REQUIRED           = 428;
    public const TOO_MANY_REQUESTS               = 429;
    public const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const UNAVAILABLE_FOR_LEGAL_REASONS   = 451;
    public const INTERNAL_SERVER_ERROR           = 500;
    public const NOT_IMPLEMENTED                 = 501;
    public const BAD_GATEWAY                     = 502;
    public const SERVICE_UNAVAILABLE             = 503;
    public const GATEWAY_TIMEOUT                 = 504;
    public const HTTP_VERSION_NOT_SUPPORTED      = 505;
    public const VARIANT_ALSO_NEGOTIATES         = 506;
    public const INSUFFICIENT_STORAGE            = 507;
    public const LOOP_DETECTED                   = 508;
    public const NOT_EXTENDED                    = 510;
    public const NETWORK_AUTHENTICATION_REQUIRED = 511;

    public const TEXTS = [
        self::CONTINUE                        => StatusText::CONTINUE,
        self::SWITCHING_PROTOCOLS             => StatusText::SWITCHING_PROTOCOLS,
        self::PROCESSING                      => StatusText::PROCESSING,
        self::OK                              => StatusText::OK,
        self::CREATED                         => StatusText::CREATED,
        self::ACCEPTED                        => StatusText::ACCEPTED,
        self::NON_AUTHORITATIVE_INFORMATION   => StatusText::NON_AUTHORITATIVE_INFORMATION,
        self::NO_CONTENT                      => StatusText::NO_CONTENT,
        self::RESET_CONTENT                   => StatusText::RESET_CONTENT,
        self::PARTIAL_CONTENT                 => StatusText::PARTIAL_CONTENT,
        self::MULTI_STATUS                    => StatusText::MULTI_STATUS,
        self::ALREADY_REPORTED                => StatusText::ALREADY_REPORTED,
        self::IM_USED                         => StatusText::IM_USED,
        self::MULTIPLE_CHOICES                => StatusText::MULTIPLE_CHOICES,
        self::MOVED_PERMANENTLY               => StatusText::MOVED_PERMANENTLY,
        self::FOUND                           => StatusText::FOUND,
        self::SEE_OTHER                       => StatusText::SEE_OTHER,
        self::NOT_MODIFIED                    => StatusText::NOT_MODIFIED,
        self::USE_PROXY                       => StatusText::USE_PROXY,
        self::TEMPORARY_REDIRECT              => StatusText::TEMPORARY_REDIRECT,
        self::PERMANENT_REDIRECT              => StatusText::PERMANENT_REDIRECT,
        self::BAD_REQUEST                     => StatusText::BAD_REQUEST,
        self::UNAUTHORIZED                    => StatusText::UNAUTHORIZED,
        self::PAYMENT_REQUIRED                => StatusText::PAYMENT_REQUIRED,
        self::FORBIDDEN                       => StatusText::FORBIDDEN,
        self::NOT_FOUND                       => StatusText::NOT_FOUND,
        self::METHOD_NOT_ALLOWED              => StatusText::METHOD_NOT_ALLOWED,
        self::NOT_ACCEPTABLE                  => StatusText::NOT_ACCEPTABLE,
        self::PROXY_AUTHENTICATION_REQUIRED   => StatusText::PROXY_AUTHENTICATION_REQUIRED,
        self::REQUEST_TIMEOUT                 => StatusText::REQUEST_TIMEOUT,
        self::CONFLICT                        => StatusText::CONFLICT,
        self::GONE                            => StatusText::GONE,
        self::LENGTH_REQUIRED                 => StatusText::LENGTH_REQUIRED,
        self::PRECONDITION_FAILED             => StatusText::PRECONDITION_FAILED,
        self::PAYLOAD_TOO_LARGE               => StatusText::PAYLOAD_TOO_LARGE,
        self::URI_TOO_LONG                    => StatusText::URI_TOO_LONG,
        self::UNSUPPORTED_MEDIA_TYPE          => StatusText::UNSUPPORTED_MEDIA_TYPE,
        self::RANGE_NOT_SATISFIABLE           => StatusText::RANGE_NOT_SATISFIABLE,
        self::EXPECTATION_FAILED              => StatusText::EXPECTATION_FAILED,
        self::I_AM_A_TEAPOT                   => StatusText::I_AM_A_TEAPOT,
        self::MISDIRECTED_REQUEST             => StatusText::MISDIRECTED_REQUEST,
        self::UNPROCESSABLE_ENTITY            => StatusText::UNPROCESSABLE_ENTITY,
        self::LOCKED                          => StatusText::LOCKED,
        self::FAILED_DEPENDENCY               => StatusText::FAILED_DEPENDENCY,
        self::UPGRADE_REQUIRED                => StatusText::UPGRADE_REQUIRED,
        self::PRECONDITION_REQUIRED           => StatusText::PRECONDITION_REQUIRED,
        self::TOO_MANY_REQUESTS               => StatusText::TOO_MANY_REQUESTS,
        self::REQUEST_HEADER_FIELDS_TOO_LARGE => StatusText::REQUEST_HEADER_FIELDS_TOO_LARGE,
        self::UNAVAILABLE_FOR_LEGAL_REASONS   => StatusText::UNAVAILABLE_FOR_LEGAL_REASONS,
        self::INTERNAL_SERVER_ERROR           => StatusText::INTERNAL_SERVER_ERROR,
        self::NOT_IMPLEMENTED                 => StatusText::NOT_IMPLEMENTED,
        self::BAD_GATEWAY                     => StatusText::BAD_GATEWAY,
        self::SERVICE_UNAVAILABLE             => StatusText::SERVICE_UNAVAILABLE,
        self::GATEWAY_TIMEOUT                 => StatusText::GATEWAY_TIMEOUT,
        self::HTTP_VERSION_NOT_SUPPORTED      => StatusText::HTTP_VERSION_NOT_SUPPORTED,
        self::VARIANT_ALSO_NEGOTIATES         => StatusText::VARIANT_ALSO_NEGOTIATES,
        self::INSUFFICIENT_STORAGE            => StatusText::INSUFFICIENT_STORAGE,
        self::LOOP_DETECTED                   => StatusText::LOOP_DETECTED,
        self::NOT_EXTENDED                    => StatusText::NOT_EXTENDED,
        self::NETWORK_AUTHENTICATION_REQUIRED => StatusText::NETWORK_AUTHENTICATION_REQUIRED,
    ];

    /**
     * Determine if a status code is valid.
     *
     * @param int $code The code
     */
    public static function isValid(int $code): bool
    {
        return $code >= self::MIN && $code <= self::MAX;
    }
}
