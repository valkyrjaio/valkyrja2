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

namespace Valkyrja\Routing\Middleware;

use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Validation\Validator;

/**
 * Class ValidateRequestMiddleware.
 *
 * @author Melech Mizrachi
 */
abstract class ValidateRequestMiddleware extends Middleware
{
    /**
     * @inheritDoc
     */
    public static function before(Request $request): Request|Response
    {
        /** @var Validator $validator */
        $validator = self::getContainer()->getSingleton(Validator::class);
        $validator->setRules(static::getRules($request));

        if (! $validator->validate()) {
            return static::getResponse($request, $validator);
        }

        return $request;
    }

    /**
     * Get the response on validation failure.
     *
     * @param Request   $request   The request
     * @param Validator $validator The validator
     */
    abstract protected static function getResponse(Request $request, Validator $validator): Response;

    /**
     * Get the validation rules.
     *
     * <code>
     *      $rules = [
     *          'title' => [
     *              'subject' => $request->getParsedBodyParam('title'),
     *              'rules' => [
     *                  'required' => [
     *                      'arguments'    => [],
     *                      'errorMessage' => 'Title is required.',
     *                  ],
     *                  'notEmpty' => [
     *                      'arguments'    => [],
     *                      'errorMessage' => 'Title must not be empty.',
     *                  ],
     *                  'min' => [
     *                      'arguments'    => [20],
     *                      'errorMessage' => 'Title must be at least 20 characters long.',
     *                  ],
     *                  'max' => [
     *                      'arguments'    => [500],
     *                      'errorMessage' => 'Title must be not be longer than 500 characters.',
     *                  ],
     *              ]
     *          ],
     *      ]
     * </code>
     *
     * @param Request $request The request
     */
    abstract protected static function getRules(Request $request): array;
}
