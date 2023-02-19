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

use Exception;
use Valkyrja\Annotation\Annotators\Annotator;
use Valkyrja\Application\Applications\Valkyrja;
use Valkyrja\Client\Client;
use Valkyrja\Config\Config;
use Valkyrja\Console\Dispatchers\Console;
use Valkyrja\Console\Inputs\Input;
use Valkyrja\Console\Kernels\Kernel as ConsoleKernel;
use Valkyrja\Console\Outputs\Output;
use Valkyrja\Container\Managers\Container;
use Valkyrja\Event\Dispatchers\Events;
use Valkyrja\Filesystem\Managers\Filesystem;
use Valkyrja\Http\Exceptions\HttpException;
use Valkyrja\Http\Exceptions\HttpRedirectException;
use Valkyrja\Http\Factories\ResponseFactory;
use Valkyrja\Http\Requests\Request;
use Valkyrja\Http\Responses\JsonResponse;
use Valkyrja\Http\Responses\RedirectResponse;
use Valkyrja\Http\Responses\Response;
use Valkyrja\HttpKernel\Kernels\Kernel;
use Valkyrja\Log\Managers\Logger;
use Valkyrja\Routing\Dispatchers\Router;
use Valkyrja\Routing\Route;
use Valkyrja\Session\Managers\Session;
use Valkyrja\Support\Directory;
use Valkyrja\View\Managers\View;

use function get_class;
use function is_string;
use function Valkyrja\abort;
use function Valkyrja\app;
use function Valkyrja\appPath;
use function Valkyrja\basePath;
use function Valkyrja\cachePath;
use function Valkyrja\configPath;
use function Valkyrja\env;
use function Valkyrja\json;
use function Valkyrja\logger;
use function Valkyrja\publicPath;
use function Valkyrja\redirect;
use function Valkyrja\redirectRoute;
use function Valkyrja\redirectTo;
use function Valkyrja\resourcesPath;
use function Valkyrja\responseFactory;
use function Valkyrja\routeUrl;
use function Valkyrja\storagePath;
use function Valkyrja\testsPath;
use function Valkyrja\vendorPath;
use function Valkyrja\view;

/**
 * Test the functionality of the helper functions.
 *
 * @author Melech Mizrachi
 */
class HelpersTest extends TestCase
{
    /**
     * The sub path.
     */
    protected string $subPath = '/sub/path';

    /**
     * Test the app() static helper method.
     */
    public function testApp(): void
    {
        self::assertEquals(true, app() instanceof Valkyrja);
    }

    /**
     * Test the container() helper method.
     */
    public function testContainer(): void
    {
        self::assertEquals(true, \Valkyrja\container() instanceof Container);
    }

    /**
     * Test the events() helper method.
     */
    public function testEvents(): void
    {
        self::assertEquals(true, \Valkyrja\events() instanceof Events);
    }

    /**
     * Test the env() helper method.
     */
    public function testEnv(): void
    {
        self::assertEquals(true, is_string(env()));
    }

    /**
     * Test the config() helper method.
     */
    public function testConfig(): void
    {
        self::assertEquals(true, \Valkyrja\config() instanceof Config);
    }

    /**
     * Test the abort() helper method.
     */
    public function testAbort(): void
    {
        try {
            abort();
        } catch (Exception $exception) {
            self::assertEquals(HttpException::class, get_class($exception));
        }
    }

    /**
     * Test the redirectTo() helper method.
     */
    public function testRedirectTo(): void
    {
        try {
            redirectTo();
        } catch (Exception $exception) {
            self::assertEquals(HttpRedirectException::class, get_class($exception));
        }
    }

    /**
     * Test the annotations() helper method.
     */
    public function testAnnotations(): void
    {
        self::assertEquals(true, \Valkyrja\annotator() instanceof Annotator);
    }

    /**
     * Test the client() helper method.
     */
    public function testClient(): void
    {
        self::assertEquals(true, \Valkyrja\client() instanceof Client);
    }

    /**
     * Test the console() helper method.
     */
    public function testConsole(): void
    {
        self::assertEquals(true, \Valkyrja\console() instanceof Console);
    }

    /**
     * Test the consoleKernel() helper method.
     */
    public function testConsoleKernel(): void
    {
        self::assertEquals(true, \Valkyrja\consoleKernel() instanceof ConsoleKernel);
    }

    /**
     * Test the filesystem() helper method.
     */
    public function testFilesystem(): void
    {
        self::assertEquals(true, \Valkyrja\filesystem() instanceof Filesystem);
    }

    /**
     * Test the input() helper method.
     */
    public function testInput(): void
    {
        self::assertEquals(true, \Valkyrja\input() instanceof Input);
    }

    /**
     * Test the kernel() helper method.
     */
    public function testKernel(): void
    {
        self::assertEquals(true, \Valkyrja\kernel() instanceof Kernel);
    }

    /**
     * Test the logger() helper method.
     */
    public function testLogger(): void
    {
        self::assertEquals(true, logger() instanceof Logger);
    }

    /**
     * Test the output() helper method.
     */
    public function testOutput(): void
    {
        self::assertEquals(true, \Valkyrja\output() instanceof Output);
    }

    /**
     * Test the request() helper method.
     */
    public function testRequest(): void
    {
        self::assertEquals(true, \Valkyrja\request() instanceof Request);
    }

    /**
     * Test the router() helper method.
     */
    public function testRouter(): void
    {
        self::assertEquals(true, \Valkyrja\router() instanceof Router);
    }

    /**
     * Test the route() helper method.
     */
    public function testRoute(): void
    {
        self::assertEquals(true, \Valkyrja\route('welcome') instanceof Route);
    }

    /**
     * Test the routeUrl() helper method.
     */
    public function testRouteUrl(): void
    {
        self::assertEquals('/', routeUrl('welcome'));
    }

    /**
     * Test the response() helper method.
     */
    public function testResponse(): void
    {
        self::assertEquals(true, \Valkyrja\response() instanceof Response);
    }

    /**
     * Test the response() helper method with arguments.
     */
    public function testResponseWithArgs(): void
    {
        self::assertEquals(true, \Valkyrja\response('test') instanceof Response);
    }

    /**
     * Test the json() helper method.
     */
    public function testJson(): void
    {
        self::assertEquals(true, json() instanceof JsonResponse);
    }

    /**
     * Test the json() helper method with arguments.
     */
    public function testJsonWithArgs(): void
    {
        self::assertEquals(true, json(['test' => 'value']) instanceof JsonResponse);
    }

    /**
     * Test the redirect() helper method.
     */
    public function testRedirect(): void
    {
        self::assertEquals(true, redirect() instanceof RedirectResponse);
    }

    /**
     * Test the redirect() helper method with arguments.
     */
    public function testRedirectWithArgs(): void
    {
        self::assertEquals(true, redirect('/') instanceof RedirectResponse);
    }

    /**
     * Test the redirectRoute() helper method.
     */
    public function testRedirectRoute(): void
    {
        self::assertEquals(true, redirectRoute('welcome') instanceof RedirectResponse);
    }

    /**
     * Test the responseBuilder() helper method.
     */
    public function testResponseBuilder(): void
    {
        self::assertEquals(true, responseFactory() instanceof ResponseFactory);
    }

    /**
     * Test the session() helper method.
     */
    public function testSession(): void
    {
        self::assertEquals(true, \Valkyrja\session() instanceof Session);
    }

    /**
     * Test the view() helper method.
     */
    public function testView(): void
    {
        self::assertEquals(true, view() instanceof View);
    }

    /**
     * Test the basePath directory helper method.
     */
    public function testBasePath(): void
    {
        self::assertEquals(Directory::$BASE_PATH, basePath());
    }

    /**
     * Test the basePath directory helper method with a sub path.
     */
    public function testBasePathSubPath(): void
    {
        $expected = Directory::$BASE_PATH . $this->subPath;

        self::assertEquals($expected, basePath($this->subPath));
    }

    /**
     * Test the appPath directory helper method.
     */
    public function testAppPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$APP_PATH;

        self::assertEquals($expected, appPath());
    }

    /**
     * Test the appPath directory helper method with a sub path.
     */
    public function testAppPathSubPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$APP_PATH . $this->subPath;

        self::assertEquals($expected, appPath($this->subPath));
    }

    /**
     * Test the cachePath directory helper method.
     */
    public function testCachePath(): void
    {
        $expected = Directory::$BASE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$STORAGE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$FRAMEWORK_STORAGE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$CACHE_PATH;

        self::assertEquals($expected, cachePath());
    }

    /**
     * Test the cachePath directory helper method with a sub path.
     */
    public function testCachePathSubPath(): void
    {
        $expected = Directory::$BASE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$STORAGE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$FRAMEWORK_STORAGE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$CACHE_PATH
            . $this->subPath;

        self::assertEquals($expected, cachePath($this->subPath));
    }

    /**
     * Test the configPath directory helper method.
     */
    public function testConfigPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$CONFIG_PATH;

        self::assertEquals($expected, configPath());
    }

    /**
     * Test the configPath directory helper method with a sub path.
     */
    public function testConfigPathSubPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$CONFIG_PATH . $this->subPath;

        self::assertEquals($expected, configPath($this->subPath));
    }

    /**
     * Test the publicPath directory helper method.
     */
    public function testPublicPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$PUBLIC_PATH;

        self::assertEquals($expected, publicPath());
    }

    /**
     * Test the publicPath directory helper method with a sub path.
     */
    public function testPublicPathSubPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$PUBLIC_PATH . $this->subPath;

        self::assertEquals($expected, publicPath($this->subPath));
    }

    /**
     * Test the resourcesPath directory helper method.
     */
    public function testResourcesPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$RESOURCES_PATH;

        self::assertEquals($expected, resourcesPath());
    }

    /**
     * Test the resourcesPath directory helper method with a sub path.
     */
    public function testResourcesPathSubPath(): void
    {
        $expected =
            Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$RESOURCES_PATH . $this->subPath;

        self::assertEquals($expected, resourcesPath($this->subPath));
    }

    /**
     * Test the storagePath directory helper method.
     */
    public function testStoragePath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$STORAGE_PATH;

        self::assertEquals($expected, storagePath());
    }

    /**
     * Test the storagePath directory helper method with a sub path.
     */
    public function testStoragePathSubPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$STORAGE_PATH . $this->subPath;

        self::assertEquals($expected, storagePath($this->subPath));
    }

    /**
     * Test the testsPath directory helper method.
     */
    public function testTestsPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$TESTS_PATH;

        self::assertEquals($expected, testsPath());
    }

    /**
     * Test the testsPath directory helper method with a sub path.
     */
    public function testTestsPathSubPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$TESTS_PATH . $this->subPath;

        self::assertEquals($expected, testsPath($this->subPath));
    }

    /**
     * Test the vendorPath directory helper method.
     */
    public function testVendorPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$VENDOR_PATH;

        self::assertEquals($expected, vendorPath());
    }

    /**
     * Test the vendorPath directory helper method with a sub path.
     */
    public function testVendorPathSubPath(): void
    {
        $expected = Directory::$BASE_PATH . Directory::DIRECTORY_SEPARATOR . Directory::$VENDOR_PATH . $this->subPath;

        self::assertEquals($expected, vendorPath($this->subPath));
    }
}
