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

namespace Valkyrja\Console\Command;

use JsonException;
use Valkyrja\Console\CacheableConsole;
use Valkyrja\Console\Commander\Commander;
use Valkyrja\Console\Constant\ExitCode;
use Valkyrja\Console\Support\Provides;
use Valkyrja\Container\CacheableContainer;
use Valkyrja\Event\Collection\CacheableCollection as CacheableEvents;
use Valkyrja\Event\Collection\Contract\Collection;
use Valkyrja\Http\Routing\Collection\CacheableCollection;
use Valkyrja\Type\BuiltIn\Support\Arr;

use function file_put_contents;
use function in_array;
use function is_file;
use function json_decode;
use function json_encode;
use function unlink;
use function Valkyrja\config;
use function Valkyrja\console;
use function Valkyrja\container;
use function Valkyrja\output;
use function Valkyrja\router;
use function var_export;

use const JSON_THROW_ON_ERROR;
use const LOCK_EX;
use const PHP_EOL;

/**
 * Class Optimize.
 *
 * @author Melech Mizrachi
 */
class Optimize extends Commander
{
    use Provides;

    /**
     * The command.
     */
    public const COMMAND           = 'optimize';
    public const PATH              = self::COMMAND;
    public const SHORT_DESCRIPTION = 'Optimize the application';

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function run(): int
    {
        /** @var array{app: array{debug: bool, env: string}, container: array<string, mixed>, console: array<string, mixed>, event: array<string, mixed>, routing: array<string, mixed>, cacheFilePath: string} $configCache */
        $configCache = config();

        $cacheFilePath = $configCache['cacheFilePath'];

        // If the cache file already exists, delete it
        if (is_file($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        $configCache['app']['debug'] = false;
        $configCache['app']['env']   = 'production';

        /** @var CacheableContainer $container */
        $container = container();
        /** @var CacheableConsole $console */
        $console = console();
        /** @var CacheableEvents $events */
        $events = container()->getSingleton(Collection::class);
        /** @var CacheableCollection $collection */
        $collection = router()->getCollection();

        $containerCache = $container->getCacheable();
        $consoleCache   = $console->getCacheable();
        $eventsCache    = $events->getCacheable();
        $routesCache    = $collection->getCacheable();

        $configCache['container']        = $containerCache;
        $configCache['console']['cache'] = $consoleCache;
        $configCache['event']['cache']   = $eventsCache;
        $configCache['routing']['cache'] = $routesCache;

        $configCache['container']['useCache'] = true;
        $configCache['console']['useCache']   = true;
        $configCache['event']['useCache']     = true;
        $configCache['routing']['useCache']   = true;

        /** @var array{container: array{providers: string[], cache: array{provided: string[]}, ...},...} $asArray */
        $asArray = json_decode(json_encode($configCache, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);

        foreach ($asArray['container']['providers'] as $key => $provider) {
            if (in_array($provider, $asArray['container']['cache']['provided'], true)) {
                unset($asArray['container']['providers'][$key]);
            }
        }

        $asString = '<?php return ' . var_export(Arr::newWithoutNull($asArray), true) . ';' . PHP_EOL;
        // $serialized = serialize($configCache);
        // $serialized = preg_replace('/O:\d+:"[^"]++"/', 'O:8:"stdClass"', $serialized);

        // Get the results of the cache attempt
        $result = file_put_contents($cacheFilePath, $asString, LOCK_EX);

        if ($result === false) {
            output()->writeMessage('An error occurred while optimizing the application.', true);

            return ExitCode::FAILURE;
        }

        output()->writeMessage('Application optimized successfully', true);

        return ExitCode::SUCCESS;
    }
}
