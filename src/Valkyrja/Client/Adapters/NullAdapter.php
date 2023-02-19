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

use Valkyrja\Client\Adapter as Contract;
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Http\ResponseFactory;

/**
 * Class NullAdapter.
 *
 * @author Melech Mizrachi
 */
class NullAdapter implements Contract
{
    /**
     * The response factory.
     */
    protected ResponseFactory $responseFactory;

    /**
     * The config.
     */
    protected array $config;

    /**
     * NullAdapter constructor.
     *
     * @param ResponseFactory $responseFactory The response factory
     * @param array           $config          The config
     */
    public function __construct(ResponseFactory $responseFactory, array $config)
    {
        $this->responseFactory = $responseFactory;
        $this->config          = $config;
    }

    /**
     * @inheritDoc
     */
    public function request(Request $request): Response
    {
        return $this->responseFactory->createResponse();
    }

    /**
     * @inheritDoc
     */
    public function get(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     */
    public function post(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     */
    public function head(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     */
    public function put(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     */
    public function patch(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }

    /**
     * @inheritDoc
     */
    public function delete(Request $request): Response
    {
        return $this->request($request->withMethod(RequestMethod::GET));
    }
}
