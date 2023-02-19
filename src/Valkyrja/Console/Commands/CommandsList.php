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

namespace Valkyrja\Console\Commands;

use Valkyrja\Console\Command;
use Valkyrja\Console\Commanders\Commander;
use Valkyrja\Console\Enums\ExitCode;
use Valkyrja\Console\Inputs\Argument;
use Valkyrja\Console\Support\Provides;

use function array_merge;
use function explode;
use function max;
use function strlen;
use function usort;
use function Valkyrja\console;
use function Valkyrja\input;
use function Valkyrja\output;

/**
 * Class CommandsList.
 *
 * @author Melech Mizrachi
 */
class CommandsList extends Commander
{
    use Provides;

    /**
     * The command.
     */
    public const COMMAND           = 'commands';
    public const PATH              = '[' . self::COMMAND . '][ {namespace:[a-zA-Z0-9]+}]';
    public const SHORT_DESCRIPTION = 'List all the commands';
    public const DESCRIPTION       = 'List all the commands';

    /**
     * @inheritDoc
     *
     * @param string|null $namespace [optional] The namespace to show commands for
     */
    public function run(string $namespace = null): int
    {
        $commands        = console()->all();
        $longestLength   = 0;
        $previousSection = '';

        $this->filterCommands($commands, $longestLength, $namespace);

        $this->applicationMessage();
        $this->sectionDivider();

        $this->usageMessage('command [options] [arguments]');

        $this->optionsSection(...input()->getGlobalOptions());
        $this->sectionDivider();

        $this->sectionTitleMessage('Commands' . ($namespace ? " for the \"$namespace\" namespace" : ''));

        foreach ($commands as $command) {
            if ($namespace === null) {
                $this->commandSection($command, $previousSection);
            }

            $this->sectionMessage(
                (! $namespace ? static::TAB : '') . ($command->getName() ?? ''),
                $command->getDescription() ?? '',
                $longestLength + 2
            );
        }

        return ExitCode::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function getArguments(): array
    {
        return [
            new Argument('namespace', 'The namespace to list commands for'),
        ];
    }

    /**
     * Filter the commands by type and name.
     *
     * @param array       $commands      The commands
     * @param int         $longestLength The longest length
     * @param string|null $namespace     [optional] The namespace to show commands
     *                                   for
     */
    protected function filterCommands(array &$commands, int &$longestLength, string $namespace = null): void
    {
        $globalCommands = [];

        /** @var Command $command */
        foreach ($commands as $key => $command) {
            $name = $command->getName();

            if (! $name) {
                continue;
            }

            $parts            = explode(':', $name);
            $commandName      = $parts[1] ?? null;
            $commandNamespace = $commandName ? $parts[0] : 'global';

            // If there was a namespace passed to the command (commands
            // namespace)  and the namespace for this command doesn't match
            // what was passed then get rid of it so only commands in the
            // namespace are shown.
            if ($commandNamespace !== $namespace && $namespace !== null) {
                unset($commands[$key]);

                continue;
            }

            $longestLength = max(strlen($name), $longestLength);

            // If this is a global namespaced command
            if ($commandNamespace === 'global') {
                // Set it in the global commands array so when we show the list
                // of commands global commands will be at the top
                $globalCommands[] = $command;

                // Unset from the commands list to avoid duplicates
                unset($commands[$key]);
            }
        }

        // Sort the global commands by name
        $this->sortCommands($globalCommands);
        // Sort the rest of the commands by name
        $this->sortCommands($commands);

        // Set the commands as the merged results of the global and other
        // commands with the global commands at the top of the list
        $commands = array_merge($globalCommands, $commands);
    }

    /**
     * The command section.
     *
     * @param Command $command         The current command
     * @param string  $previousSection The previous section
     */
    protected function commandSection(Command $command, string &$previousSection): void
    {
        $parts          = explode(':', $command->getName() ?? '');
        $commandName    = $parts[1] ?? null;
        $currentSection = $commandName ? $parts[0] : 'global';

        if ($previousSection !== $currentSection) {
            output()->getFormatter()->cyan();
            output()->writeMessage(static::TAB);
            output()->writeMessage($currentSection, true);

            $previousSection = $currentSection;
        }
    }

    /**
     * Sort commands by name.
     *
     * @param array $commands The commands
     */
    protected function sortCommands(array &$commands): void
    {
        usort(
            $commands,
            static function (Command $item1, Command $item2) {
                return $item1->getName() <=> $item2->getName();
            }
        );
    }
}
