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

namespace Valkyrja\Http\Factories;

use InvalidArgumentException;
use JsonException;
use Valkyrja\Container\Container;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\Constants\StreamType;
use Valkyrja\Http\JsonResponse;
use Valkyrja\Http\RedirectResponse;
use Valkyrja\Http\Response;
use Valkyrja\Http\ResponseFactory as Contract;
use Valkyrja\Http\Streams\Stream as HttpStream;
use Valkyrja\Routing\Url;
use Valkyrja\View\View;

/**
 * Class ResponseFactory.
 *
 * @author Melech Mizrachi
 */
class ResponseFactory implements Contract
{
    /**
     * ResponseBuilder constructor.
     */
    public function __construct(
        protected Container $container
    ) {
    }

    /**
     * @inheritDoc
     */
    public function createResponse(string $content = null, int $statusCode = null, array $headers = null): Response
    {
        $stream = new HttpStream(StreamType::TEMP, 'wb+');
        $stream->write($content ?? '');
        $stream->rewind();

        return new \Valkyrja\Http\Responses\Response($stream, $statusCode ?? StatusCode::OK, $headers ?? []);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function createJsonResponse(array $data = null, int $statusCode = null, array $headers = null): JsonResponse
    {
        return new \Valkyrja\Http\Responses\JsonResponse(
            $data ?? [],
            $statusCode ?? StatusCode::OK,
            $headers ?? []
        );
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException
     * @throws JsonException
     */
    public function createJsonpResponse(string $callback, array $data = null, int $statusCode = null, array $headers = null): JsonResponse
    {
        return $this->createJsonResponse($data, $statusCode, $headers)->withCallback($callback);
    }

    /**
     * @inheritDoc
     */
    public function createRedirectResponse(string $uri = null, int $statusCode = null, array $headers = null): RedirectResponse
    {
        return new \Valkyrja\Http\Responses\RedirectResponse(
            $uri ?? '/',
            $statusCode ?? StatusCode::OK,
            $headers ?? []
        );
    }

    /**
     * @inheritDoc
     */
    public function route(string $name, array $data = null, int $statusCode = null, array $headers = null): RedirectResponse
    {
        /** @var Url $url */
        $url = $this->container->getSingleton(Url::class);

        // Get the uri from the router using the route and parameters
        $uri = $url->getUrl($name, $data);

        return $this->createRedirectResponse($uri, $statusCode, $headers);
    }

    /**
     * @inheritDoc
     */
    public function view(string $template, array $data = null, int $statusCode = null, array $headers = null): Response
    {
        /** @var View $view */
        $view = $this->container->getSingleton(View::class);

        $content = $view->createTemplate($template, $data ?? [])->render();

        return $this->createResponse($content, $statusCode, $headers);
    }
}
