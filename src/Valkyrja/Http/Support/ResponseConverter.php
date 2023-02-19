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

namespace Valkyrja\Http\Support;

use Psr\Http\Message\ResponseInterface;
use Valkyrja\Http\Facades\ResponseFactory;
use Valkyrja\Http\Response;

/**
 * Class ResponseConverter.
 *
 * @author Melech Mizrachi
 */
class ResponseConverter
{
    /**
     * Convert a PSR7 Response to Valkyrja Response.
     *
     * @param ResponseInterface $psr7Response The PSR7 Response
     */
    public static function fromPsr7(ResponseInterface $psr7Response): Response
    {
        return ResponseFactory::createResponse(
            $psr7Response->getBody()->getContents(),
            $psr7Response->getStatusCode(),
            $psr7Response->getHeaders()
        );
    }
}
