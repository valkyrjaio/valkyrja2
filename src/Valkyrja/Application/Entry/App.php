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

namespace Valkyrja\Application\Entry;

use Valkyrja\Application\Application;
use Valkyrja\Application\Applications\Valkyrja;
use Valkyrja\Application\Env;
use Valkyrja\Config\Config\Config;
use Valkyrja\Http\Factories\RequestFactory;
use Valkyrja\Http\Request;
use Valkyrja\Support\Directory;

use function define;

/**
 * Class App.
 *
 * @author Melech Mizrachi
 */
abstract class App
{
    /**
     * Start the application.
     *
     * @param string               $dir    The directory
     * @param class-string<Env>    $env    The env class to use
     * @param class-string<Config> $config The config class to use
     */
    public static function start(string $dir, string $env, string $config): Application
    {
        static::appStart($dir, $env, $config);
        static::directory($dir, $env, $config);
        static::env($dir, $env, $config);

        return static::app($dir, $env, $config);
    }

    /**
     * Now that the application has been bootstrapped and setup correctly with all our requirements lets run it!
     *
     * @param string               $dir    The directory
     * @param class-string<Env>    $env    The env class to use
     * @param class-string<Config> $config The config class to use
     */
    public static function http(string $dir, string $env, string $config): void
    {
        $app = static::start($dir, $env, $config);

        $app->kernel()->run(static::getRequest());
    }

    /**
     * Now that the application has been bootstrapped and setup correctly with all our requirements lets run it!
     *
     * @param string               $dir    The directory
     * @param class-string<Env>    $env    The env class to use
     * @param class-string<Config> $config The config class to use
     */
    public static function console(string $dir, string $env, string $config): never
    {
        $app = static::start($dir, $env, $config);

        $exitCode = $app->consoleKernel()->run();

        static::exitConsole($exitCode);
    }

    /**
     * Set a global constant for when the application as a whole started.
     *
     * @param string               $dir    The directory
     * @param class-string<Env>    $env    The env class to use
     * @param class-string<Config> $config The config class to use
     */
    protected static function appStart(string $dir, string $env, string $config): void
    {
        define('APP_START', microtime(true));
    }

    /**
     * Let's set the base directory within the web server for our application
     * so that when we locate directories and files within the application
     * we have a standard location from which to do so.
     *
     * @param string               $dir    The directory
     * @param class-string<Env>    $env    The env class to use
     * @param class-string<Config> $config The config class to use
     */
    protected static function directory(string $dir, string $env, string $config): void
    {
        Directory::$BASE_PATH = $dir;
    }

    /**
     * Let's setup the application by bootstrapping it. This will set the
     * correct environment class file to use, and appropriate the config
     * that should be loaded by the application. In dev you'll want to
     * use the default config out of the root config directory, but
     * when you're on a production environment definitely have
     * your config cached and the flag set in your env class.
     *
     * @param string               $dir    The directory
     * @param class-string<Env>    $env    The env class to use
     * @param class-string<Config> $config The config class to use
     */
    protected static function env(string $dir, string $env, string $config): void
    {
        Valkyrja::setEnv($env);
    }

    /**
     * Let's start up the application by creating a new instance of the
     * application class. This is going to bind all the various
     * components together into a singular hub.
     *
     * @param string               $dir    The directory
     * @param class-string<Env>    $env    The env class to use
     * @param class-string<Config> $config The config class to use
     */
    protected static function app(string $dir, string $env, string $config): Application
    {
        return new Valkyrja($config);
    }

    /**
     * Get the request.
     */
    protected static function getRequest(): Request
    {
        return RequestFactory::fromGlobals();
    }

    /**
     * Exit to let the terminal know we're done.
     *
     * @param int $exitCode The exit code
     */
    protected static function exitConsole(int $exitCode): never
    {
        exit($exitCode);
    }
}
