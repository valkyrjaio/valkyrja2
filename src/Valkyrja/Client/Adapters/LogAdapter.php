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

use JsonException;
use Valkyrja\Client\LogAdapter as Contract;
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Http\ResponseFactory;
use Valkyrja\Log\Driver as Logger;
use Valkyrja\Type\Support\Obj;

/**
 * Class LogAdapter.
 *
 * @author Melech Mizrachi
 */
class LogAdapter implements Contract
{
    /**
     * The logger.
     */
    protected Logger $logger;

    /**
     * The response factory.
     */
    protected ResponseFactory $responseFactory;

    /**
     * The client config.
     */
    protected array $config;

    /**
     * LogAdapter constructor.
     *
     * @param Logger          $logger          The logger
     * @param ResponseFactory $responseFactory The response factory
     * @param array           $config          The config
     */
    public function __construct(Logger $logger, ResponseFactory $responseFactory, array $config)
    {
        $this->logger          = $logger;
        $this->responseFactory = $responseFactory;
        $this->config          = $config;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function request(Request $request): Response
    {
        $optionsString = Obj::toString($request);

        $this->logger->info(
            static::class . " request: {$request->getMethod()}, uri {$request->getUri()->__toString()}, options $optionsString"
        );

        return $this->responseFactory->createResponse();
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function get(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function post(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::POST));
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function head(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::HEAD));
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function put(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::PUT));
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function patch(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::PATCH));
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function delete(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::DELETE));
    }
}
