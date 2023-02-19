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

namespace Valkyrja\Validation\Rules;

use Valkyrja\Type\Support\Integer;
use Valkyrja\Type\Support\Str;
use Valkyrja\Validation\Exceptions\ValidationException;

use function in_array;
use function is_bool;
use function is_numeric;

/**
 * Class Base.
 *
 * @author Melech Mizrachi
 */
class Base
{
    /**
     * Subject must have a value.
     *
     * @param mixed $subject The subject
     *
     * @throws ValidationException
     */
    public function required(mixed $subject): void
    {
        if (! $subject) {
            throw new ValidationException("$subject is required");
        }
    }

    /**
     * Subject must equal a given value.
     *
     * @param mixed $subject The subject
     * @param mixed $value   The value
     *
     * @throws ValidationException
     */
    public function equals(mixed $subject, mixed $value): void
    {
        if ($subject !== $value) {
            throw new ValidationException("$subject must equal $value");
        }
    }

    /**
     * Subject must be empty.
     *
     * @param string|null $subject [optional] The subject
     *
     * @throws ValidationException
     */
    public function empty(string $subject = null): void
    {
        if ($subject) {
            throw new ValidationException("$subject must be empty");
        }
    }

    /**
     * Subject must not be empty.
     *
     * @param mixed $subject The subject
     *
     * @throws ValidationException
     */
    public function notEmpty(mixed $subject): void
    {
        if (! $subject) {
            throw new ValidationException("$subject must not be empty");
        }
    }

    /**
     * Subject must be alphabetic.
     *
     * @param mixed $subject The subject
     *
     * @throws ValidationException
     */
    public function alpha(mixed $subject): void
    {
        if (! Str::isAlphabetic((string) $subject)) {
            throw new ValidationException("$subject must be alphabetic");
        }
    }

    /**
     * Subject must be lowercase.
     *
     * @param mixed $subject The subject
     *
     * @throws ValidationException
     */
    public function lowercase(mixed $subject): void
    {
        if (! Str::isLowercase((string) $subject)) {
            throw new ValidationException("$subject must be lowercase");
        }
    }

    /**
     * Subject must be uppercase.
     *
     * @param mixed $subject The subject
     *
     * @throws ValidationException
     */
    public function uppercase(mixed $subject): void
    {
        if (! Str::isUppercase((string) $subject)) {
            throw new ValidationException("$subject must be uppercase");
        }
    }

    /**
     * Subject must be above a minimum length.
     *
     * @param string $subject The subject
     * @param int    $min     [optional] The minimum length
     *
     * @throws ValidationException
     */
    public function min(string $subject, int $min = 0): void
    {
        if (! Str::min($subject, $min)) {
            throw new ValidationException("$subject must be longer than $min");
        }
    }

    /**
     * Subject must be under a maximum length.
     *
     * @param string $subject The subject
     * @param int    $max     [optional] The maximum length
     *
     * @throws ValidationException
     */
    public function max(string $subject, int $max = 255): void
    {
        if (! Str::max($subject, $max)) {
            throw new ValidationException("$subject must not be longer than $max");
        }
    }

    /**
     * Subject must start with a substring.
     *
     * @param string $subject The subject
     * @param string $needle  The needle the subject must start with
     *
     * @throws ValidationException
     */
    public function startsWith(string $subject, string $needle): void
    {
        if (! Str::startsWith($subject, $needle)) {
            throw new ValidationException("$subject must start with $needle");
        }
    }

    /**
     * Subject ends with a substring.
     *
     * @param string $subject The subject
     * @param string $needle  The needle the subject must end with
     *
     * @throws ValidationException
     */
    public function endsWith(string $subject, string $needle): void
    {
        if (! Str::endsWith($subject, $needle)) {
            throw new ValidationException("$subject must end with $needle");
        }
    }

    /**
     * Subject contains given substrings.
     *
     * @param string         $subject The subject
     * @param string[]|int[] $needles The needles contained in the subject
     *
     * @throws ValidationException
     */
    public function contains(string $subject, string|int ...$needles): void
    {
        foreach ($needles as $needle) {
            if (! str_contains($subject, (string) $needle)) {
                throw new ValidationException("$subject must contain $needle");
            }
        }
    }

    /**
     * Subject contains any given substring.
     *
     * @param string         $subject The subject
     * @param string[]|int[] $needles The needles contained in the subject
     *
     * @throws ValidationException
     */
    public function containsAny(string $subject, string|int ...$needles): void
    {
        foreach ($needles as $needle) {
            if (str_contains($subject, (string) $needle)) {
                return;
            }
        }

        $needlesString = implode(', ', $needles);

        throw new ValidationException("$subject must one of: $needlesString");
    }

    /**
     * Subject is an email.
     *
     * @param string $subject The subject
     *
     * @throws ValidationException
     */
    public function email(string $subject): void
    {
        if (! Str::isEmail($subject)) {
            throw new ValidationException("$subject is not a valid email");
        }
    }

    /**
     * Subject is a boolean.
     *
     * @param mixed $subject The subject
     *
     * @throws ValidationException
     */
    public function boolean(mixed $subject): void
    {
        if (! is_bool($subject)) {
            throw new ValidationException("$subject must be a boolean");
        }
    }

    /**
     * Subject is numeric.
     *
     * @param mixed $subject The subject
     *
     * @throws ValidationException
     */
    public function numeric(mixed $subject): void
    {
        if (! is_numeric($subject)) {
            throw new ValidationException("$subject must be numeric");
        }
    }

    /**
     * Subject is less than a maximum value.
     *
     * @param int $subject The subject
     * @param int $max     [optional] The value the subject must be less than
     *
     * @throws ValidationException
     */
    public function lessThan(int $subject, int $max = 0): void
    {
        if (! Integer::lessThan($subject, $max)) {
            throw new ValidationException("$subject must be less than $max");
        }
    }

    /**
     * Subject is more than a minimum value.
     *
     * @param int $subject The subject
     * @param int $min     [optional] The value the subject must be greater than
     *
     * @throws ValidationException
     */
    public function greaterThan(int $subject, int $min = 0): void
    {
        if (! Integer::greaterThan($subject, $min)) {
            throw new ValidationException("$subject must be greater than $min");
        }
    }

    /**
     * Subject is one of a set of valid values.
     *
     * @param mixed $subject     The subject
     * @param array $validValues The valid values
     *
     * @throws ValidationException
     */
    public function oneOf(mixed $subject, mixed ...$validValues): void
    {
        if (! in_array($subject, $validValues, true)) {
            throw new ValidationException("$subject must be one of");
        }
    }

    /**
     * Subject matches a regex.
     *
     * @param string $subject The subject
     * @param string $regex   The regex
     *
     * @throws ValidationException
     */
    public function regex(string $subject, string $regex): void
    {
        if (! preg_match($regex, $subject)) {
            throw new ValidationException("$subject must match the given regex $regex");
        }
    }
}
