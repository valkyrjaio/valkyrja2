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

namespace Valkyrja\Routing\Commands;

use InvalidArgumentException;
use Valkyrja\Console\Commanders\Commander;
use Valkyrja\Console\Enums\FormatForeground;
use Valkyrja\Console\Enums\FormatOption;
use Valkyrja\Console\Support\Provides;
use Valkyrja\Routing\Route;

use function implode;
use function max;
use function str_repeat;
use function strlen;
use function usort;
use function Valkyrja\output;
use function Valkyrja\router;

/**
 * Class RoutesList.
 *
 * @author Melech Mizrachi
 */
class RoutesList extends Commander
{
    use Provides;

    /**
     * The command.
     */
    public const COMMAND           = 'routes:list';
    public const PATH              = self::COMMAND;
    public const SHORT_DESCRIPTION = 'List all routes';
    public const DESCRIPTION       = '';

    protected const INVERT_FORMAT = "\e[" . FormatOption::INVERSE . 'm';
    protected const END_FORMAT    = "\e[0m";

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException
     */
    public function run(): int
    {
        $routerRoutes = router()->getRoutes();
        $routes       = [];
        $headerTexts  = [
            'Request Methods',
            'Path',
            'Name',
            'Dispatch',
            'Regex',
        ];
        $lengths      = [
            strlen($headerTexts[0]),
            strlen($headerTexts[1]),
            strlen($headerTexts[2]),
            strlen($headerTexts[3]),
            strlen($headerTexts[4]),
        ];

        // Sort routes by path
        usort($routerRoutes, static fn (Route $a, Route $b) => $a->getPath() <=> $b->getPath());

        foreach ($routerRoutes as $route) {
            $this->setRoute($route, $routes, $lengths);
        }

        $sepLine = $this->getSepLine($lengths);
        $odd     = false;

        output()->writeMessage($this->oddFormat(true) . $sepLine, true);
        $this->headerMessage($headerTexts, $lengths);
        output()->writeMessage($sepLine, true);

        foreach ($routes as $key => $route) {
            $routeMessage = '| '
                . $route[0]
                . str_repeat(' ', $lengths[0] - strlen($route[0]))
                . ' | '
                . $route[1]
                . str_repeat(' ', $lengths[1] - strlen($route[1]))
                . ' | '
                . $route[2]
                . str_repeat(' ', $lengths[2] - strlen($route[2]))
                . ' | '
                . $route[3]
                . str_repeat(' ', $lengths[3] - strlen($route[3]))
                . ' | '
                . $route[4]
                . str_repeat(' ', $lengths[4] - strlen($route[4]))
                . ' |';

            $odd          = ((int) $key) % 2 > 0;
            $routeMessage = $this->oddFormat($odd) . $routeMessage;

            output()->writeMessage($routeMessage . static::END_FORMAT, true);
        }

        output()->writeMessage($this->oddFormat(! $odd) . $sepLine . static::END_FORMAT, true);

        return 0;
    }

    /**
     * Set a route as an array from a route object.
     *
     * @param Route $route   The route object
     * @param array $routes  The flat routes
     * @param array $lengths The longest string lengths
     *
     * @throws InvalidArgumentException
     */
    protected function setRoute(Route $route, array &$routes, array &$lengths): void
    {
        $requestMethod = implode(' | ', $route->getMethods());
        $dispatch      = 'Closure';
        $regex         = $route->getRegex() ?? '';
        $path          = $route->getPath();
        $name          = $route->getName() ?? '';

        if ($requestMethod === 'GET | HEAD | POST | PUT | PATCH | CONNECT | OPTIONS | TRACE | DELETE') {
            $requestMethod = 'ANY';
        }

        if (($function = $route->getFunction()) !== null) {
            $dispatch = $function;
        } elseif (null !== $class = $route->getClass()) {
            $dispatch = $class
                . ($route->isStatic() ? '::' : '->')
                . (($method = $route->getMethod())
                    ? $method . '()'
                    : $route->getProperty() ?? '');
        }

        $lengths[0] = max($lengths[0], strlen($requestMethod));
        $lengths[1] = max($lengths[1], strlen($path));
        $lengths[2] = max($lengths[2], strlen($name));
        $lengths[3] = max($lengths[3], strlen($dispatch));
        $lengths[4] = max($lengths[4], strlen($regex));

        $routes[] = [
            $requestMethod,
            $path,
            $name,
            $dispatch,
            $regex,
        ];
    }

    /**
     * Get the separation line.
     *
     * @param array $lengths The longest lengths
     */
    protected function getSepLine(array $lengths): string
    {
        return '+-' . str_repeat('-', $lengths[0])
            . '-+-' . str_repeat('-', $lengths[1])
            . '-+-' . str_repeat('-', $lengths[2])
            . '-+-' . str_repeat('-', $lengths[3])
            . '-+-' . str_repeat('-', $lengths[4])
            . '-+';
    }

    /**
     * Format odd rows.
     */
    protected function oddFormat(bool $odd): string
    {
        return $odd
            ? static::INVERT_FORMAT . "\e[" . FormatForeground::CYAN->value . 'm'
            : static::INVERT_FORMAT . "\e[" . FormatForeground::LIGHT_CYAN->value . 'm';
    }

    /**
     * Output the header message.
     *
     * @param array $headerTexts The header texts
     * @param array $lengths     The longest lengths
     */
    protected function headerMessage(array $headerTexts, array $lengths): void
    {
        $headerMessage = '| ' . $headerTexts[0]
            . str_repeat(' ', $lengths[0] - strlen($headerTexts[0]))
            . ' | ' . $headerTexts[1]
            . str_repeat(' ', $lengths[1] - strlen($headerTexts[1]))
            . ' | ' . $headerTexts[2]
            . str_repeat(' ', $lengths[2] - strlen($headerTexts[2]))
            . ' | ' . $headerTexts[3]
            . str_repeat(' ', $lengths[3] - strlen($headerTexts[3]))
            . ' | ' . $headerTexts[4]
            . str_repeat(' ', $lengths[4] - strlen($headerTexts[4]))
            . ' |';

        output()->writeMessage($headerMessage, true);
    }
}
