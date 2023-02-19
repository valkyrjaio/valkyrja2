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

namespace Valkyrja\Tests\Functional;

use Valkyrja\Annotation\Annotator;
use Valkyrja\Application\Applications\Valkyrja;
use Valkyrja\Client\Client;
use Valkyrja\Config\Commands\ConfigCache;
use Valkyrja\Config\Constants\ConfigKey;
use Valkyrja\Console\Console;
use Valkyrja\Console\Kernel as ConsoleKernel;
use Valkyrja\Container\Managers\Container;
use Valkyrja\Dispatcher\Dispatchers\Dispatcher;
use Valkyrja\Event\Dispatchers\Events;
use Valkyrja\Filesystem\Filesystem;
use Valkyrja\Http\ResponseFactory;
use Valkyrja\HttpKernel\Kernel;
use Valkyrja\Log\Logger;
use Valkyrja\Path\PathGenerator;
use Valkyrja\Path\PathParser;
use Valkyrja\Routing\Router;
use Valkyrja\Session\Session;
use Valkyrja\Tests\Config;
use Valkyrja\Tests\EnvTest;
use Valkyrja\Tests\Unit\Support\ProviderClass;
use Valkyrja\View\View;

use function is_string;
use function unlink;

/**
 * Test the functionality of the Application.
 *
 * @author Melech Mizrachi
 */
class ApplicationTest extends TestCase
{
    /**
     * Test the Application construct.
     */
    public function testConstruct(): void
    {
        self::assertEquals(true, $this->app instanceof Valkyrja);
    }

    /**
     * Test the app() static helper method.
     */
    public function testApp(): void
    {
        self::assertEquals(true, Valkyrja::app() instanceof Valkyrja);
    }

    /**
     * Test the container() helper method.
     */
    public function testContainer(): void
    {
        self::assertEquals(true, $this->app->container() instanceof Container);
    }

    /**
     * Test the dispatcher() helper method.
     */
    public function testDispatcher(): void
    {
        self::assertEquals(true, $this->app->dispatcher() instanceof Dispatcher);
    }

    /**
     * Test the events() helper method.
     */
    public function testEvents(): void
    {
        self::assertEquals(true, $this->app->events() instanceof Events);
    }

    /**
     * Test the version() helper method.
     */
    public function testVersion(): void
    {
        self::assertEquals(Valkyrja::VERSION, $this->app->version());
    }

    /**
     * Test the config() helper method.
     */
    public function testConfig(): void
    {
        self::assertEquals(true, $this->app->config() instanceof Config);
    }

    /**
     * Test the addConfig() helper method.
     */
    public function testAddConfig(): void
    {
        $config = new Config();
        $this->app->addConfig($config, 'new');

        self::assertEquals(true, isset($this->app->config()['new']));
    }

    /**
     * Test the env() helper method.
     */
    public function testEnv(): void
    {
        self::assertEquals(true, is_string($this->app::env()));
    }

    /**
     * Test the env() helper method.
     */
    public function testEnvValue(): void
    {
        self::assertEquals(true, $this->app::env('CONSOLE_QUIET'));
    }

    /**
     * Test the getEnv() helper method.
     */
    public function testGetEnv(): void
    {
        self::assertEquals(EnvTest::class, $this->app::getEnv());
    }

    /**
     * Test the getEnv() helper method.
     */
    public function testSetEnv(): void
    {
        $this->app::setEnv(EnvTest::class);
        $this->assertEquals(EnvTest::class, $this->app::getEnv());
    }

    /**
     * Test the environment() helper method.
     */
    public function testEnvironment(): void
    {
        self::assertEquals($this->app->config()['app']['env'], $this->app->environment());
    }

    /**
     * Test the debug() helper method.
     */
    public function testDebug(): void
    {
        self::assertEquals($this->app->config()['app']['debug'], $this->app->debug());
    }

    /**
     * Test the annotations() helper method.
     */
    public function testAnnotations(): void
    {
        self::assertEquals(true, $this->app[Annotator::class] instanceof Annotator);
    }

    /**
     * Test the client() helper method.
     */
    public function testClient(): void
    {
        self::assertEquals(true, $this->app[Client::class] instanceof Client);
    }

    /**
     * Test the console() helper method.
     */
    public function testConsole(): void
    {
        self::assertEquals(true, $this->app[Console::class] instanceof Console);
    }

    /**
     * Test the consoleKernel() helper method.
     */
    public function testConsoleKernel(): void
    {
        self::assertEquals(true, $this->app[ConsoleKernel::class] instanceof ConsoleKernel);
    }

    /**
     * Test the filesystem() helper method.
     */
    public function testFilesystem(): void
    {
        self::assertEquals(true, $this->app[Filesystem::class] instanceof Filesystem);
    }

    /**
     * Test the kernel() helper method.
     */
    public function testKernel(): void
    {
        self::assertEquals(true, $this->app[Kernel::class] instanceof Kernel);
    }

    /**
     * Test the pathGenerator() helper method.
     */
    public function testPathGenerator(): void
    {
        self::assertEquals(true, $this->app[PathGenerator::class] instanceof PathGenerator);
    }

    /**
     * Test the pathParser() helper method.
     */
    public function testPathParser(): void
    {
        self::assertEquals(true, $this->app[PathParser::class] instanceof PathParser);
    }

    /**
     * Test the logger() helper method.
     */
    public function testLogger(): void
    {
        self::assertEquals(true, $this->app[Logger::class] instanceof Logger);
    }

    /**
     * Test the router() helper method.
     */
    public function testRouter(): void
    {
        self::assertEquals(true, $this->app[Router::class] instanceof Router);
    }

    /**
     * Test the responseBuilder() helper method.
     */
    public function testResponseBuilder(): void
    {
        self::assertEquals(true, $this->app[ResponseFactory::class] instanceof ResponseFactory);
    }

    /**
     * Test the session() helper method.
     */
    public function testSession(): void
    {
        self::assertEquals(true, $this->app[Session::class] instanceof Session);
    }

    /**
     * Test the view() helper method.
     */
    public function testView(): void
    {
        self::assertEquals(true, $this->app[View::class] instanceof View);
    }

    /**
     * Test the application setup being called a second time without forcing.
     */
    public function testSetupTwice(): void
    {
        // Try to re-setup the application without forcing
        $this->app->setup(ConfigTest::class);

        // It shouldn't have used the new config settings and kept the old
        // so debug should still be false
        self::assertEquals(false, $this->app->debug());
    }

    /**
     * Test the application setup with debug on.
     */
    public function testDebugOn(): void
    {
        $config = new Config();

        $config->app->debug = true;
        $this->app          = $this->app->withConfig($config);

        self::assertEquals(true, $this->app->debug());
    }

    /**
     * Test resetting the application with a config provider.
     */
    public function testApplicationSetupWithConfigProvider(): void
    {
        $config            = new Config();
        $config->providers = [
            ProviderClass::class,
        ];

        $this->app = $this->app->withConfig($config);

        self::assertEquals(ProviderClass::class, $this->app->config()['providers'][0]);

        $this->app = $this->app->withConfig(new Config());
    }

    /**
     * Test resetting the application with a config provider.
     */
    public function testApplicationSetupWithCachedConfig(): void
    {
        /** @var Console $console */
        $console = $this->app[Console::class];
        // Get the config cache command
        $configCacheCommand = $console->matchCommand(ConfigCache::COMMAND);
        // Run the config cache command
        $console->dispatchCommand($configCacheCommand);

        // Resetup the app with the new config and force
        $this->app->setup(ConfigTest::class);

        // Because the app will use the config cache the forced changes to the config made above shouldn't
        // take effect and the value for app.debug should still be false.
        self::assertEquals(false, $this->app->debug());

        // Delete the config cache file to avoid headaches later
        unlink($this->app->config(ConfigKey::CONFIG_CACHE_FILE_PATH));

        // Reset the application to normal operations
        $this->app = $this->app->withConfig(new Config());
    }
}
