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

namespace Valkyrja\Test\Assert;

use Valkyrja\Test\Assert\Contract\Asserter as Contract;
use Valkyrja\Test\Assert\Enum\ResultType;
use Valkyrja\Test\Exception\AssertFailureException;
use Valkyrja\Test\Exception\AssertWarningException;
use Valkyrja\Type\BuiltIn\Enum\Support\Enum;

/**
 * Class Asserter.
 *
 * @author Melech Mizrachi
 */
abstract class Asserter implements Contract
{
    /**
     * The assertions.
     *
     * @var string[]
     */
    protected array $assertions = [];

    /**
     * The errors.
     *
     * @var AssertFailureException[]
     */
    protected array $errors = [];

    /**
     * The successes.
     *
     * @var string[]
     */
    protected array $successes = [];

    /**
     * The warnings.
     *
     * @var AssertWarningException[]
     */
    protected array $warnings = [];

    /**
     * @inheritDoc
     */
    public function getAssertions(): array
    {
        return $this->assertions;
    }

    /**
     * @inheritDoc
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    public function getSuccesses(): array
    {
        return $this->successes;
    }

    /**
     * @inheritDoc
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @inheritDoc
     */
    public function __get(string $name): mixed
    {
        if (Enum::isValidName(ResultType::class, $name)) {
            return $this->$name;
        }

        return null;
    }
}
