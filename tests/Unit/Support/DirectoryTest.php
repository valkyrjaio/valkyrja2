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

namespace Valkyrja\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Valkyrja\Support\Directory;

/**
 * Test the Directory support class.
 *
 * @author Melech Mizrachi
 */
class DirectoryTest extends TestCase
{
    /**
     * The base path.
     */
    protected string $basePath = '/base/path';

    /**
     * The sub path.
     */
    protected string $subPath = '/sub/path';

    /**
     * Setup the test.
     */
    public function setUp(): void
    {
        parent::setUp();

        Directory::$BASE_PATH = $this->basePath;
    }

    /**
     * Test the basePath directory helper method.
     */
    public function testBasePath(): void
    {
        self::assertEquals($this->basePath, Directory::basePath());
    }

    /**
     * Test the basePath directory helper method with a sub path.
     */
    public function testBasePathSubPath(): void
    {
        $expected = $this->basePath . $this->subPath;

        self::assertEquals($expected, Directory::basePath($this->subPath));
    }

    /**
     * Test the appPath directory helper method.
     */
    public function testAppPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$APP_PATH;

        self::assertEquals($expected, Directory::appPath());
    }

    /**
     * Test the appPath directory helper method with a sub path.
     */
    public function testAppPathSubPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$APP_PATH . $this->subPath;

        self::assertEquals($expected, Directory::appPath($this->subPath));
    }

    /**
     * Test the cachePath directory helper method.
     */
    public function testCachePath(): void
    {
        $expected = $this->basePath
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$STORAGE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$FRAMEWORK_STORAGE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$CACHE_PATH;

        self::assertEquals($expected, Directory::cachePath());
    }

    /**
     * Test the cachePath directory helper method with a sub path.
     */
    public function testCachePathSubPath(): void
    {
        $expected = $this->basePath
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$STORAGE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$FRAMEWORK_STORAGE_PATH
            . Directory::DIRECTORY_SEPARATOR
            . Directory::$CACHE_PATH
            . $this->subPath;

        self::assertEquals($expected, Directory::cachePath($this->subPath));
    }

    /**
     * Test the configPath directory helper method.
     */
    public function testConfigPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$CONFIG_PATH;

        self::assertEquals($expected, Directory::configPath());
    }

    /**
     * Test the configPath directory helper method with a sub path.
     */
    public function testConfigPathSubPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$CONFIG_PATH . $this->subPath;

        self::assertEquals($expected, Directory::configPath($this->subPath));
    }

    /**
     * Test the publicPath directory helper method.
     */
    public function testPublicPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$PUBLIC_PATH;

        self::assertEquals($expected, Directory::publicPath());
    }

    /**
     * Test the publicPath directory helper method with a sub path.
     */
    public function testPublicPathSubPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$PUBLIC_PATH . $this->subPath;

        self::assertEquals($expected, Directory::publicPath($this->subPath));
    }

    /**
     * Test the resourcesPath directory helper method.
     */
    public function testResourcesPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$RESOURCES_PATH;

        self::assertEquals($expected, Directory::resourcesPath());
    }

    /**
     * Test the resourcesPath directory helper method with a sub path.
     */
    public function testResourcesPathSubPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$RESOURCES_PATH . $this->subPath;

        self::assertEquals($expected, Directory::resourcesPath($this->subPath));
    }

    /**
     * Test the storagePath directory helper method.
     */
    public function testStoragePath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$STORAGE_PATH;

        self::assertEquals($expected, Directory::storagePath());
    }

    /**
     * Test the storagePath directory helper method with a sub path.
     */
    public function testStoragePathSubPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$STORAGE_PATH . $this->subPath;

        self::assertEquals($expected, Directory::storagePath($this->subPath));
    }

    /**
     * Test the testsPath directory helper method.
     */
    public function testTestsPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$TESTS_PATH;

        self::assertEquals($expected, Directory::testsPath());
    }

    /**
     * Test the testsPath directory helper method with a sub path.
     */
    public function testTestsPathSubPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$TESTS_PATH . $this->subPath;

        self::assertEquals($expected, Directory::testsPath($this->subPath));
    }

    /**
     * Test the vendorPath directory helper method.
     */
    public function testVendorPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$VENDOR_PATH;

        self::assertEquals($expected, Directory::vendorPath());
    }

    /**
     * Test the vendorPath directory helper method with a sub path.
     */
    public function testVendorPathSubPath(): void
    {
        $expected = $this->basePath . Directory::DIRECTORY_SEPARATOR . Directory::$VENDOR_PATH . $this->subPath;

        self::assertEquals($expected, Directory::vendorPath($this->subPath));
    }

    /**
     * Test the path helper.
     */
    public function testPath(): void
    {
        self::assertEquals($this->subPath, Directory::path($this->subPath));
    }

    /**
     * Test the path helper with a path that has no forward slash.
     */
    public function testPathNoForwardSlash(): void
    {
        self::assertEquals($this->subPath, Directory::path('sub/path'));
    }

    /**
     * Test the path helper with a null input value.
     */
    public function testPathNull(): void
    {
        self::assertEquals(null, Directory::path());
    }
}
