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

namespace Valkyrja\Routing\Actions;

use Valkyrja\Http\Request;
use Valkyrja\Validation\Validator;

/**
 * Abstract Class ValidationAction.
 *
 * @author Melech Mizrachi
 */
abstract class ValidationAction
{
    /**
     * The request.
     */
    protected Request $request;

    /**
     * The validator.
     */
    protected Validator $validator;

    /**
     * Action constructor.
     *
     * @param Request   $request   The request
     * @param Validator $validator The validator
     */
    public function __construct(Request $request, Validator $validator)
    {
        $this->request   = $request;
        $this->validator = $validator;
    }

    /**
     * Validate against a set of rules.
     */
    protected function validate(): bool
    {
        $this->validator->setRules($this->getRules());

        return $this->validator->validate();
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
