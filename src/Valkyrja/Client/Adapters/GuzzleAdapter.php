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

namespace Valkyrja\Client\Adapters;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Valkyrja\Client\GuzzleAdapter as Contract;
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Http\JsonRequest;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Http\ResponseFactory;

/**
 * Class GuzzleAdapter.
 *
 * @author Melech Mizrachi
 */
class GuzzleAdapter implements Contract
{
    /**
     * GuzzleAdapter constructor.
     *
     * @param ClientInterface $guzzle          The guzzle client
     * @param ResponseFactory $responseFactory The response factory
     * @param array           $config          The client config
     */
    public function __construct(
        protected ClientInterface $guzzle,
        protected ResponseFactory $responseFactory,
        protected array $config
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws GuzzleException
     */
    public function request(Request $request): Response
    {
        return $this->fromPsr7($this->getGuzzleResponse($request));
    }

    /**
     * @inheritDoc
     *
     * @throws GuzzleException
     */
    public function get(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     *
     * @throws GuzzleException
     */
    public function post(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     *
     * @throws GuzzleException
     */
    public function head(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     *
     * @throws GuzzleException
     */
    public function put(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     *
     * @throws GuzzleException
     */
    public function patch(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     *
     * @throws GuzzleException
     */
    public function delete(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * Get a Guzzle response from a Valkyrja request.
     *
     * @param Request $request The request
     *
     * @throws GuzzleException
     */
    protected function getGuzzleResponse(Request $request): ResponseInterface
    {
        $options = $this->config['options'] ?? [];

        $this->setGuzzleHeaders($request, $options);
        $this->setGuzzleCookies($request, $options);
        $this->setGuzzleFormParams($request, $options);
        $this->setGuzzleBody($request, $options);

        return $this->guzzle->request($request->getMethod(), $request->getUri()->__toString(), $options);
    }

    /**
     * Set the Guzzle headers.
     *
     * @param Request $request  The request
     * @param array   &$options The options
     */
    protected function setGuzzleHeaders(Request $request, array &$options): void
    {
        if ($headers = $request->getHeaders()) {
            $options['headers'] = [];

            foreach ($headers as $header => $value) {
                $options['headers'][$header] = $value;
            }
        }
    }

    /**
     * Set the Guzzle cookies.
     *
     * @param Request $request  The request
     * @param array   &$options The options
     */
    protected function setGuzzleCookies(Request $request, array &$options): void
    {
        if ($cookies = $request->getCookieParams()) {
            $jar = new CookieJar();

            foreach ($cookies as $name => $value) {
                $guzzleCookie = new SetCookie();

                $guzzleCookie->setName($name);
                $guzzleCookie->setValue($value);

                $jar->setCookie($guzzleCookie);
            }

            $options['cookies'] = $jar;
        }
    }

    /**
     * Set the Guzzle form params.
     *
     * @param Request $request  The request
     * @param array   &$options The options
     */
    protected function setGuzzleFormParams(Request $request, array &$options): void
    {
        if (($body = $request->getParsedBody()) && ! ($request instanceof JsonRequest && $request->getParsedJson())) {
            $options['form_params'] = $body;
        }
    }

    /**
     * Set the Guzzle body.
     *
     * @param Request $request  The request
     * @param array   &$options The options
     */
    protected function setGuzzleBody(Request $request, array &$options): void
    {
        $body = $request->getBody();
        $body->rewind();

        if ($contents = $body->getContents()) {
            $options['body'] = $contents;
        }
    }

    /**
     * Convert a Guzzle Response to Valkyrja Response.
     *
     * @param ResponseInterface $guzzleResponse The Guzzle Response
     */
    protected function fromPsr7(ResponseInterface $guzzleResponse): Response
    {
        return $this->responseFactory->createResponse(
            $guzzleResponse->getBody()->getContents(),
            $guzzleResponse->getStatusCode(),
            $guzzleResponse->getHeaders()
        );
    }
}
