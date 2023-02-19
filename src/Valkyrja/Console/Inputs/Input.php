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

namespace Valkyrja\Console\Inputs;

use Valkyrja\Console\Input as Contract;

use function array_merge;
use function explode;
use function implode;
use function in_array;
use function is_array;

/**
 * Class Input.
 *
 * @author Melech Mizrachi
 */
class Input implements Contract
{
    /**
     * The request arguments.
     */
    protected array $inputArguments = [];

    /**
     * The arguments.
     */
    protected array $arguments = [];

    /**
     * The short options.
     */
    protected array $shortOptions = [];

    /**
     * The long options.
     */
    protected array $longOptions = [];

    /**
     * Input constructor.
     *
     * @param array $arguments [optional] The input arguments
     */
    public function __construct(array $arguments = [])
    {
        $this->inputArguments = $arguments;

        $this->parseRequestArguments();
    }

    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @inheritDoc
     */
    public function getShortOptions(): array
    {
        return $this->shortOptions;
    }

    /**
     * @inheritDoc
     */
    public function getLongOptions(): array
    {
        return $this->longOptions;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return array_merge($this->longOptions, $this->shortOptions);
    }

    /**
     * @inheritDoc
     */
    public function getStringArguments(): string
    {
        $arguments       = $this->inputArguments;
        $globalArguments = $this->getGlobalOptionsFlat();

        foreach ($arguments as $key => $argument) {
            if (in_array($argument, $globalArguments, true)) {
                unset($arguments[$key]);
            }
        }

        return implode(' ', $arguments);
    }

    /**
     * @inheritDoc
     */
    public function getInputArguments(): array
    {
        return $this->inputArguments;
    }

    /**
     * @inheritDoc
     */
    public function getArgument(string $argument): ?string
    {
        return $this->arguments[$argument] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasArgument(string $argument): bool
    {
        return isset($this->arguments[$argument]);
    }

    /**
     * @inheritDoc
     */
    public function getShortOption(string $option): ?string
    {
        return $this->shortOptions[$option] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasShortOption(string $option): bool
    {
        return isset($this->shortOptions[$option]);
    }

    /**
     * @inheritDoc
     */
    public function getLongOption(string $option): ?string
    {
        return $this->longOptions[$option] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasLongOption(string $option): bool
    {
        return isset($this->longOptions[$option]);
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $option): ?string
    {
        return $this->shortOptions[$option] ?? $this->longOptions[$option] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasOption(string $option): bool
    {
        return $this->hasShortOption($option) || $this->hasLongOption($option);
    }

    /**
     * @inheritDoc
     */
    public function getGlobalOptions(): array
    {
        return [
            new Option('help', 'The help option for the command', 'h'),
            new Option('quiet', 'Do not output to the console', 'q'),
            new Option('version', 'The version of this application', 'V'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getGlobalOptionsFlat(): array
    {
        return [
            '-h',
            '--help',
            '-q',
            '--quite',
            '-V',
            '--version',
        ];
    }

    /**
     * Parse request arguments to split by options and arguments.
     */
    protected function parseRequestArguments(): void
    {
        // Iterate through the request arguments
        foreach ($this->inputArguments as $argument) {
            // Split the string on an equal sign
            $exploded = explode('=', $argument);

            $key   = $exploded[0];
            $value = $exploded[1] ?? true;
            $type  = 'arguments';

            // If the key has double dash it is a long option
            if (str_contains($key, '--')) {
                $type = 'longOptions';
            } // If the key has a single dash it is a short option
            elseif (str_contains($key, '-')) {
                $type = 'shortOptions';
            }

            // If the key is already set
            if (isset($this->{$type}[$key])) {
                // If the key isn't already an array
                if (! is_array($this->{$type}[$key])) {
                    // Make it an array with the current value
                    $this->{$type}[$key] = [$this->{$type}[$key]];
                }

                // Add the next value to the array
                $this->{$type}[$key][] = $value;

                continue;
            }

            // Set the key value pair
            $this->{$type}[$key] = $value;
        }
    }
}
