<?php

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 *-------------------------------------------------------------------------
 * Application Configuration
 *-------------------------------------------------------------------------
 *
 * This part of the configuration has to do with the base configuration
 * settings for the application as a whole.
     */
return [
    /*
     *-------------------------------------------------------------------------
     * Application Environment
     *-------------------------------------------------------------------------
     *
     * //
     */
    'env'                => env('APP_ENV', 'production'),

    /*
     *-------------------------------------------------------------------------
     * Application Debug
     *-------------------------------------------------------------------------
     *
     * //
     */
    'debug'              => env('APP_DEBUG', false),

    /*
     *-------------------------------------------------------------------------
     * Application Url
     *-------------------------------------------------------------------------
     *
     * //
     */
    'url'                => env('APP_URL', 'localhost'),

    /*
     *-------------------------------------------------------------------------
     * Application Timezone
     *-------------------------------------------------------------------------
     *
     * //
     */
    'timezone'           => env('APP_TIMEZONE', 'UTC'),

    /*
     *-------------------------------------------------------------------------
     * Application Version
     *-------------------------------------------------------------------------
     *
     * //
     */
    'version'            => env('APP_VERSION', Valkyrja\Application::VERSION),

    /*
     *-------------------------------------------------------------------------
     * Application Key
     *-------------------------------------------------------------------------
     *
     * //
     */
    'key'                => env('APP_KEY', 'some_secret_app_key'),

    /*
     *-------------------------------------------------------------------------
     * Application Http Exception Class
     *-------------------------------------------------------------------------
     *
     * //
     *
     */
    'httpExceptionClass' => env('APP_HTTP_EXCEPTION_CLASS', \Valkyrja\Http\Exceptions\HttpException::class),

    /*
     *-------------------------------------------------------------------------
     * Application Container Class
     *-------------------------------------------------------------------------
     *
     * //
     */
    'container'          => env('APP_CONTAINER', Valkyrja\Container\NativeContainer::class),

    /*
     *-------------------------------------------------------------------------
     * Application Dispatcher Class
     *-------------------------------------------------------------------------
     *
     * //
     */
    'dispatcher'         => env('APP_DISPATCHER', Valkyrja\Dispatcher\NativeDispatcher::class),

    /*
     *-------------------------------------------------------------------------
     * Application Events Class
     *-------------------------------------------------------------------------
     *
     * //
     */
    'events'             => env('APP_EVENTS', Valkyrja\Events\NativeEvents::class),

    /*
     *-------------------------------------------------------------------------
     * Application ExceptionHandler Class
     *-------------------------------------------------------------------------
     *
     * //
     */
    'exceptionHandler'             => env('APP_EXCEPTION_HANDLER', Valkyrja\Exceptions\NativeExceptionHandler::class),

    /*
     *-------------------------------------------------------------------------
     * Application Path Regex Map
     *-------------------------------------------------------------------------
     *
     * //
     */
    'pathRegexMap'       => env(
        'APP_PATH_REGEX_MAP',
        [
            'num'                  => '(\d+)',
            'slug'                 => '([a-zA-Z0-9-]+)',
            'alpha'                => '([a-zA-Z]+)',
            'alpha-lowercase'      => '([a-z]+)',
            'alpha-uppercase'      => '([A-Z]+)',
            'alpha-num'            => '([a-zA-Z0-9]+)',
            'alpha-num-underscore' => '(\w+)',
        ]
    ),
];
