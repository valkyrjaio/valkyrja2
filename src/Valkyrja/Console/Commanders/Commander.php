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

namespace Valkyrja\Console\Commanders;

use Valkyrja\Application\Application;
use Valkyrja\Console\Commander as Contract;
use Valkyrja\Console\Enums\ArgumentMode;
use Valkyrja\Console\Enums\ExitCode;
use Valkyrja\Console\Inputs\Argument;
use Valkyrja\Console\Inputs\Option;

use function max;
use function str_repeat;
use function strlen;
use function Valkyrja\output;

/**
 * Abstract Class Handler.
 *
 * @author Melech Mizrachi
 */
abstract class Commander implements Contract
{
    /**
     * The command.
     */
    public const COMMAND           = '';
    public const PATH              = '';
    public const SHORT_DESCRIPTION = '';
    public const DESCRIPTION       = '';

    /**
     * Tabbing structure to use.
     */
    protected const TAB        = '  ';
    protected const DOUBLE_TAB = self::TAB . self::TAB;

    /**
     * @inheritDoc
     */
    public static function getCommand(): string
    {
        return static::COMMAND;
    }

    /**
     * @inheritDoc
     */
    public static function getPath(): string
    {
        return static::PATH;
    }

    /**
     * @inheritDoc
     */
    public static function getShortDescription(): string
    {
        return static::SHORT_DESCRIPTION;
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return static::DESCRIPTION;
    }

    /**
     * @inheritDoc
     */
    public function help(): int
    {
        $this->usageMessage();
        $this->argumentsSection();
        $this->optionsSection();

        return ExitCode::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    public function run(): int
    {
        return ExitCode::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    public function version(): int
    {
        $this->applicationMessage();

        return ExitCode::SUCCESS;
    }

    /**
     * The usage message and description.
     *
     * @param string|null $message [optional] The usage to use instead of default
     */
    protected function usageMessage(string $message = null): void
    {
        $message = $message ?? $this->usagePath();

        $this->sectionTitleMessage('Usage');
        output()->writeMessage(static::TAB);
        output()->writeMessage($message, true);
    }

    /**
     * The arguments section.
     *
     * @param Argument ...$arguments The argument
     */
    protected function argumentsSection(Argument ...$arguments): void
    {
        if (empty($arguments)) {
            $arguments = $this->getArguments();
        }

        if (! $arguments) {
            return;
        }

        $longestLength = 0;

        $this->sectionDivider();
        $this->sectionTitleMessage('Arguments');

        foreach ($arguments as $argument) {
            $longestLength = max(strlen($argument->getName()), $longestLength);
        }

        foreach ($arguments as $argument) {
            $this->sectionMessage(static::TAB . $argument->getName(), $argument->getDescription(), $longestLength);
        }
    }

    /**
     * The options section.
     *
     * @param Option ...$options The options
     */
    protected function optionsSection(Option ...$options): void
    {
        if (empty($options)) {
            $options = $this->getOptions();
        }

        if (! $options) {
            return;
        }

        $longestLength = 0;

        $this->sectionDivider();
        $this->sectionTitleMessage('Options');

        foreach ($options as $option) {
            $longestLength = max(strlen($this->getOptionName($option)), $longestLength);
        }

        foreach ($options as $option) {
            $this->sectionMessage($this->getOptionName($option), $option->getDescription(), $longestLength);
        }
    }

    /**
     * Get the usage path.
     */
    protected function usagePath(): string
    {
        $message = static::COMMAND;

        if ($this->getOptions()) {
            $message .= ' [options]';
        }

        foreach ($this->getArguments() as $argument) {
            $message .= ' '
                . ($argument->getMode() === ArgumentMode::OPTIONAL ? '[' : '')
                . '<'
                . $argument->getName()
                . '>'
                . ($argument->getMode() === ArgumentMode::OPTIONAL ? ']' : '');
        }

        return $message;
    }

    /**
     * The section message.
     */
    protected function sectionTitleMessage(string $sectionName): void
    {
        output()->getFormatter()->underscore();
        output()->writeMessage($sectionName . ':', true);
        output()->getFormatter()->resetOptions();
    }

    /**
     * Get the valid arguments.
     *
     * @return Argument[]
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * The sections divider.
     */
    protected function sectionDivider(): void
    {
        output()->writeMessage('', true);
    }

    /**
     * The section message.
     *
     * @param string   $name          The name
     * @param string   $description   The description
     * @param int|null $longestLength The longest item length
     */
    protected function sectionMessage(string $name, string $description, int $longestLength = null): void
    {
        $longestLength = $longestLength ?? 0;
        $spacesToAdd   = $longestLength - strlen($name);

        output()->getFormatter()->green();
        output()->writeMessage(static::TAB . $name);

        output()->getFormatter()->resetColor();

        output()->writeMessage($spacesToAdd > 0 ? str_repeat('.', $spacesToAdd) : '');
        output()->writeMessage(str_repeat('.', 8));
        output()->writeMessage($description, true);
    }

    /**
     * Get the valid options.
     *
     * @return Option[]
     */
    protected function getOptions(): array
    {
        return [];
    }

    /**
     * Get an options name for the options section.
     *
     * @param Option $option The option
     */
    protected function getOptionName(Option $option): string
    {
        $name = '';

        if ($shortcut = $option->getShortcut()) {
            $name .= '-' . $shortcut . ', ';
        } else {
            $name .= static::DOUBLE_TAB;
        }

        $name .= '--' . $option->getName();

        return $name;
    }

    /**
     * The application message.
     */
    protected function applicationMessage(): void
    {
        output()->getFormatter()->magenta();
        output()->writeMessage('Valkyrja Application');
        output()->getFormatter()->resetColor();
        output()->writeMessage(' version ');
        output()->getFormatter()->cyan();
        output()->writeMessage(Application::VERSION, true);
        output()->getFormatter()->resetColor();
    }
}
