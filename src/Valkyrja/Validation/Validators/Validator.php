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

namespace Valkyrja\Validation\Validators;

use Valkyrja\Validation\Config\Config;
use Valkyrja\Validation\Constants\Property;
use Valkyrja\Validation\Constants\Rule;
use Valkyrja\Validation\Exceptions\ValidationException;
use Valkyrja\Validation\Factory;
use Valkyrja\Validation\Validator as Contract;

/**
 * Class Validator.
 *
 * @author Melech Mizrachi
 */
class Validator implements Contract
{
    /**
     * The rules.
     *
     * @var object[]
     */
    protected static array $rules = [];

    /**
     * The rules map.
     *
     * @var array<string, class-string>
     */
    protected array $rulesMap = [];

    /**
     * The default rules.
     *
     * @var class-string
     */
    protected string $defaultRules;

    /**
     * The error messages if validation failed.
     *
     * @var array<string, string>
     */
    protected array $errorMessages = [];

    /**
     * The validation rules.
     *
     * @var array<string, array{subject: string, rules: array<string, array{arguments: array, message: string}>}>|null
     */
    protected ?array $validationRules = null;

    /**
     * Validator constructor.
     *
     * @param Config|array{
     *     rule: class-string,
     *     rulesMap: array<string, class-string>
     * }              $config
     */
    public function __construct(
        protected Factory $factory,
        protected Config|array $config
    ) {
        $this->defaultRules = $config['rule'];
        $this->rulesMap     = $config['rulesMap'];
    }

    /**
     * @inheritDoc
     *
     * @template T
     *
     * @param class-string<T>|null $name [optional] The name of the rules to get
     *
     * @return T|object
     */
    public function getRules(string $name = null): object
    {
        $name ??= $this->defaultRules;

        /** @var class-string $name */
        return self::$rules[$name]
            ??= $this->factory->createRules($name);
    }

    /**
     * @inheritDoc
     */
    public function validate(): bool
    {
        $validated = true;

        if ($this->validationRules) {
            $validated = $this->validateRules($this->validationRules);
        }

        return $validated;
    }

    /**
     * @inheritDoc
     */
    public function validateRules(array $rules): bool
    {
        $this->validateRuleSet($rules);

        return empty($this->errorMessages);
    }

    /**
     * @inheritDoc
     */
    public function setRules(array $rules): void
    {
        $this->validationRules = $rules;
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @inheritDoc
     */
    public function getFirstErrorMessage(): ?string
    {
        if (! empty($errorMessages = $this->errorMessages)) {
            return $errorMessages[array_key_first($errorMessages)];
        }

        return null;
    }

    /**
     * Validate a rule set.
     *
     * @param array<string, array{subject: string, rules: array<string, array{arguments: array, message: string}>}> $ruleSet The rule set
     */
    protected function validateRuleSet(array $ruleSet): void
    {
        foreach ($ruleSet as $key => $item) {
            $this->validateSubject($key, $item[Property::SUBJECT] ?? null, $item[Property::RULES] ?? []);
        }
    }

    /**
     * Validate a subject item.
     *
     * @param string                                                  $subjectName The subject name
     * @param mixed                                                   $subject     The subject
     * @param array<string, array{arguments: array, message: string}> $rules       The rules
     */
    protected function validateSubject(string $subjectName, mixed $subject, array $rules = []): void
    {
        // If this subject is not required and the subject is empty or not passed in
        if (! isset($rules[Rule::REQUIRED]) && ! $subject) {
            // Reset the error messages for this subject to avoid false flags
            unset($this->errorMessages[$subjectName]);

            // Don't go through the rules as we do not need to validate a non existent subject that isn't required
            return;
        }

        // Iterate through the rules
        foreach ($rules as $name => $rule) {
            // Validate the rule
            $this->validateRule($subjectName, $name, $subject, $rule);

            // If there is already a message no need to continue iterating through the rest of the rules
            if (isset($this->errorMessages[$subjectName])) {
                return;
            }
        }
    }

    /**
     * Validate a rule.
     *
     * @param string                                   $subjectName The subject name
     * @param string                                   $name        The rule name
     * @param mixed                                    $subject     The subject
     * @param array{arguments: array, message: string} $rule        The rule
     */
    protected function validateRule(string $subjectName, string $name, mixed $subject, array $rule): void
    {
        $arguments    = $rule[Property::ARGUMENTS] ?? [];
        $rulesName    = $this->rulesMap[$name] ?? null;
        $errorMessage = $rule[Property::MESSAGE] ?? null;
        // Not in try catch to avoid swallowing an error if rule doesn't exist
        $rules = $this->getRules($rulesName);

        try {
            $rules->{$name}($subject, ...$arguments);
        } catch (ValidationException $validationException) {
            $this->errorMessages[$subjectName] = $errorMessage ?? $validationException->getMessage();
        }
    }
}
