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

namespace Valkyrja\Console\Outputs;

use Valkyrja\Config\Constants\ConfigKey;
use Valkyrja\Console\Enums\OutputStyle;
use Valkyrja\Console\Formatter;
use Valkyrja\Console\Formatters\Formatter as FormatterClass;
use Valkyrja\Console\Output as Contract;

use function strip_tags;
use function Valkyrja\config;
use function Valkyrja\input;

use const PHP_EOL;

/**
 * Class Output.
 *
 * @author Melech Mizrachi
 */
class Output implements Contract
{
    /**
     * Whether to use quiet console.
     */
    private static bool $quiet = false;

    /**
     * The formatter.
     */
    protected Formatter $formatter;

    /**
     * Output constructor.
     */
    public function __construct()
    {
        $this->formatter = new FormatterClass();

        self::$quiet = config(ConfigKey::CONSOLE_QUIET) || input()->hasOption('--quiet');
    }

    /**
     * @inheritDoc
     */
    public function getFormatter(): Formatter
    {
        return $this->formatter;
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(Formatter $formatter): void
    {
        $this->formatter = $formatter;
    }

    /**
     * @inheritDoc
     */
    public function write(array $messages, bool $newLine = null, OutputStyle $outputStyle = null): void
    {
        foreach ($messages as $message) {
            $this->writeMessage($message, $newLine, $outputStyle);
        }
    }

    /**
     * @inheritDoc
     */
    public function writeMessage(string $message, bool $newLine = null, OutputStyle $outputStyle = null): void
    {
        $newLine         = $newLine ?? false;
        $outputStyleType = $outputStyle?->value ?? OutputStyle::NORMAL;

        switch ($outputStyleType) {
            case OutputStyle::NORMAL:
                $message = $this->formatter->format($message);

                break;
            case OutputStyle::RAW:
                break;
            case OutputStyle::PLAIN:
                $message = strip_tags($this->formatter->format($message));

                break;
        }

        $this->writeOut($message, $newLine);
    }

    /**
     * Write a message out to the console.
     */
    protected function writeOut(string $message, bool $newLine): void
    {
        if (self::$quiet) {
            return;
        }

        echo $message;

        if ($newLine) {
            echo PHP_EOL;
        }
    }
}
