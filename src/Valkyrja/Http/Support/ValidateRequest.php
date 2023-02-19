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

use Valkyrja\Http\Request;
use Valkyrja\Validation\Validator;

/**
 * Abstract Class ValidateRequest.
 *
 * @author Melech Mizrachi
 */
abstract class ValidateRequest
{
    /**
     * ValidateRequest constructor.
     *
     * @param Request   $request   The request
     * @param Validator $validator The validator
     */
    public function __construct(
        protected Request $request,
        protected Validator $validator
    ) {
    }

    /**
     * Validate the request.
     */
    public function validate(): bool
    {
        $this->validator->setRules($this->getRules());

        return $this->validator->validate();
    }

    /**
     * Get the request.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get the validator.
     */
    public function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * Get the validation rules.
     *
     * <code>
     *      $rules = [
     *          'title' => [
     *              'subject' => $this->request->getParsedBodyParam('title'),
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
     */
    abstract protected function getRules(): array;
}
