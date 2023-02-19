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

namespace Valkyrja\Support;

/**
 * Class Directory.
 *
 * @author Melech Mizrachi
 */
class Directory
{
    /**
     * Directory separator.
     *
     * @constant string
     */
    public const DIRECTORY_SEPARATOR = '/';

    /**
     * Base directory path.
     *
     * @constant string
     */
    public static string $BASE_PATH = '';

    /**
     * Various paths for the application.
     */
    public static string $APP_PATH               = 'app';
    public static string $BOOTSTRAP_PATH         = 'bootstrap';
    public static string $COMMANDS_PATH          = 'commands';
    public static string $EVENTS_PATH            = 'events';
    public static string $ROUTES_PATH            = 'routes';
    public static string $SERVICES_PATH          = 'services';
    public static string $CONFIG_PATH            = 'config';
    public static string $ENV_PATH               = 'env';
    public static string $PUBLIC_PATH            = 'public';
    public static string $RESOURCES_PATH         = 'resources';
    public static string $STORAGE_PATH           = 'storage';
    public static string $FRAMEWORK_STORAGE_PATH = 'framework';
    public static string $CACHE_PATH             = 'cache';
    public static string $TESTS_PATH             = 'tests';
    public static string $VENDOR_PATH            = 'vendor';

    /**
     * Get the app directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function appPath(string $path = null): string
    {
        return static::__basePath(static::$APP_PATH . static::path($path));
    }

    /**
     * Get the base directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function basePath(string $path = null): string
    {
        return static::$BASE_PATH . static::path($path);
    }

    /**
     * Construct a path with the directory separator prepended.
     *
     * @param string|null $path The path
     */
    public static function path(string $path = null): string
    {
        return $path && $path[0] !== static::DIRECTORY_SEPARATOR
            ? static::DIRECTORY_SEPARATOR . $path
            : $path ?? '';
    }

    /**
     * Get the bootstrap directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function bootstrapPath(string $path = null): string
    {
        return static::__basePath(static::$BOOTSTRAP_PATH . static::path($path));
    }

    /**
     * Get the commands config directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function commandsPath(string $path = null): string
    {
        return static::bootstrapPath(static::$COMMANDS_PATH . static::path($path));
    }

    /**
     * Get the events config directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function eventsPath(string $path = null): string
    {
        return static::bootstrapPath(static::$EVENTS_PATH . static::path($path));
    }

    /**
     * Get the routes config directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function routesPath(string $path = null): string
    {
        return static::bootstrapPath(static::$ROUTES_PATH . static::path($path));
    }

    /**
     * Get the services config directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function servicesPath(string $path = null): string
    {
        return static::bootstrapPath(static::$SERVICES_PATH . static::path($path));
    }

    /**
     * Get the config directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function configPath(string $path = null): string
    {
        return static::__basePath(static::$CONFIG_PATH . static::path($path));
    }

    /**
     * Get the env directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function envPath(string $path = null): string
    {
        return static::__basePath(static::$ENV_PATH . static::path($path));
    }

    /**
     * Get the public directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function publicPath(string $path = null): string
    {
        return static::__basePath(static::$PUBLIC_PATH . static::path($path));
    }

    /**
     * Get the resources directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function resourcesPath(string $path = null): string
    {
        return static::__basePath(static::$RESOURCES_PATH . static::path($path));
    }

    /**
     * Get the storage directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function storagePath(string $path = null): string
    {
        return static::__basePath(static::$STORAGE_PATH . static::path($path));
    }

    /**
     * Get the framework storage directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function frameworkStoragePath(string $path = null): string
    {
        return static::storagePath(static::$FRAMEWORK_STORAGE_PATH . static::path($path));
    }

    /**
     * Get the cache directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function cachePath(string $path = null): string
    {
        return static::frameworkStoragePath(static::$CACHE_PATH . static::path($path));
    }

    /**
     * Get the tests directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function testsPath(string $path = null): string
    {
        return static::__basePath(static::$TESTS_PATH . static::path($path));
    }

    /**
     * Get the vendor directory for the application.
     *
     * @param string|null $path [optional] The path to append
     */
    public static function vendorPath(string $path = null): string
    {
        return static::__basePath(static::$VENDOR_PATH . static::path($path));
    }

    /**
     * Internal get the base directory for the application unless the path starts with a directory separator (since
     *  if it does then it is already an absolute path and shouldn't be prepended with the base path).
     *
     * @param string|null $path [optional] The path to append
     */
    protected static function __basePath(string $path = null): string
    {
        if ($path && str_starts_with($path, static::DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return static::$BASE_PATH . static::path($path);
    }
}
