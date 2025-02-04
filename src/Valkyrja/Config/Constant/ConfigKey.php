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

namespace Valkyrja\Config\Constant;

/**
 * Constant ConfigKey.
 *
 * @author Melech Mizrachi
 */
final class ConfigKey
{
    public const CONFIG_FILE_PATH       = 'filePath';
    public const CONFIG_CACHE_FILE_PATH = 'cacheFilePath';
    public const CONFIG_USE_CACHE_FILE  = 'useCache';

    public const API_JSON_MODEL      = 'api.jsonModel';
    public const API_JSON_DATA_MODEL = 'api.jsonDataModel';

    public const APP_ENV                  = 'app.env';
    public const APP_DEBUG                = 'app.debug';
    public const APP_URL                  = 'app.url';
    public const APP_TIMEZONE             = 'app.timezone';
    public const APP_VERSION              = 'app.version';
    public const APP_KEY                  = 'app.key';
    public const APP_HTTP_EXCEPTION_CLASS = 'app.httpExceptionClass';
    public const APP_ERROR_HANDLER        = 'app.errorHandler';
    public const APP_PROVIDERS            = 'app.providers';

    public const ANNOTATIONS_ENABLED   = 'annotation.enabled';
    public const ANNOTATIONS_CACHE_DIR = 'annotation.cacheDir';
    public const ANNOTATIONS_MAP       = 'annotation.map';
    public const ANNOTATIONS_ALIASES   = 'annotation.aliases';

    public const AUTH_ADAPTER                = 'auth.adapter';
    public const AUTH_USER_ENTITY            = 'auth.userEntity';
    public const AUTH_REPOSITORY             = 'auth.repository';
    public const AUTH_ADAPTERS               = 'auth.adapters';
    public const AUTH_ALWAYS_AUTHENTICATE    = 'auth.alwaysAuthenticate';
    public const AUTH_KEEP_USER_FRESH        = 'auth.keepUserFresh';
    public const AUTH_AUTHENTICATE_ROUTE     = 'auth.authenticateRoute';
    public const AUTH_AUTHENTICATE_URL       = 'auth.authenticateUrl';
    public const AUTH_NOT_AUTHENTICATE_ROUTE = 'auth.notAuthenticateRoute';
    public const AUTH_NOT_AUTHENTICATE_URL   = 'auth.notAuthenticateUrl';
    public const AUTH_PASSWORD_CONFIRM_ROUTE = 'auth.confirmPasswordRoute';

    public const BROADCAST_ADAPTER  = 'broadcast.adapter';
    public const BROADCAST_ADAPTERS = 'broadcast.adapters';
    public const BROADCAST_MESSAGE  = 'broadcast.message';
    public const BROADCAST_MESSAGES = 'broadcast.messages';
    public const BROADCAST_DISKS    = 'broadcast.disks';

    public const CACHE_DEFAULT = 'cache.default';
    public const CACHE_STORES  = 'cache.stores';

    public const CLIENT_ADAPTER  = 'client.adapter';
    public const CLIENT_ADAPTERS = 'client.adapters';

    public const CONSOLE_PROVIDERS       = 'console.providers';
    public const CONSOLE_DEV_PROVIDERS   = 'console.devProviders';
    public const CONSOLE_QUIET           = 'console.quiet';
    public const CONSOLE_USE_ANNOTATIONS = 'console.useAnnotations';
    public const CONSOLE_HANDLERS        = 'console.handlers';
    public const CONSOLE_FILE_PATH       = 'console.filePath';
    public const CONSOLE_CACHE_FILE_PATH = 'console.cacheFilePath';
    public const CONSOLE_USE_CACHE_FILE  = 'console.useCache';
    public const CONSOLE_CACHE           = 'console.cache';

    public const CONTAINER_PROVIDERS        = 'container.providers';
    public const CONTAINER_DEV_PROVIDERS    = 'container.devProviders';
    public const CONTAINER_USE_ANNOTATIONS  = 'container.useAnnotations';
    public const CONTAINER_SERVICES         = 'container.services';
    public const CONTAINER_CONTEXT_SERVICES = 'container.contextServices';
    public const CONTAINER_FILE_PATH        = 'container.filePath';
    public const CONTAINER_CACHE_FILE_PATH  = 'container.cacheFilePath';
    public const CONTAINER_USE_CACHE_FILE   = 'container.useCache';
    public const CONTAINER_CACHE            = 'container.cache';

    public const CRYPT_KEY      = 'crypt.key';
    public const CRYPT_KEY_PATH = 'crypt.keyPath';
    public const CRYPT_ADAPTER  = 'crypt.adapter';
    public const CRYPT_ADAPTERS = 'crypt.adapters';

    public const EVENTS_USE_ANNOTATIONS = 'event.useAnnotations';
    public const EVENTS_CLASSES         = 'event.classes';
    public const EVENTS_FILE_PATH       = 'event.filePath';
    public const EVENTS_CACHE_FILE_PATH = 'event.cacheFilePath';
    public const EVENTS_USE_CACHE_FILE  = 'event.useCache';
    public const EVENTS_CACHE           = 'event.cache';

    public const FILESYSTEM_DEFAULT       = 'filesystem.default';
    public const FILESYSTEM_ADAPTERS      = 'filesystem.adapters';
    public const FILESYSTEM_DISKS         = 'filesystem.disks';
    public const FILESYSTEM_LOCAL_DIR     = 'filesystem.disks.local.dir';
    public const FILESYSTEM_LOCAL_ADAPTER = 'filesystem.disks.local.adapter';
    public const FILESYSTEM_S3_KEY        = 'filesystem.disks.s3.key';
    public const FILESYSTEM_S3_SECRET     = 'filesystem.disks.s3.secret';
    public const FILESYSTEM_S3_REGION     = 'filesystem.disks.s3.region';
    public const FILESYSTEM_S3_VERSION    = 'filesystem.disks.s3.version';
    public const FILESYSTEM_S3_BUCKET     = 'filesystem.disks.s3.bucket';
    public const FILESYSTEM_S3_DIR        = 'filesystem.disks.s3.dir';
    public const FILESYSTEM_S3_OPTIONS    = 'filesystem.disks.s3.options';
    public const FILESYSTEM_S3_ADAPTER    = 'filesystem.disks.s3.adapter';

    public const LOG_DEFAULT  = 'log.default';
    public const LOG_ADAPTERS = 'log.adapters';
    public const LOG_DRIVERS  = 'log.drivers';
    public const LOG_LOGGERS  = 'log.loggers';

    public const MAIL_DEFAULT          = 'mail.default';
    public const MAIL_ADAPTERS         = 'mail.adapters';
    public const MAIL_DRIVERS          = 'mail.drivers';
    public const MAIL_MAILERS          = 'mail.mailers';
    public const MAIL_DEFAULT_MESSAGE  = 'mail.defaultMessage';
    public const MAIL_MESSAGE_ADAPTERS = 'mail.messageAdapters';
    public const MAIL_MESSAGES         = 'mail.messages';

    public const NOTIFICATION_NOTIFICATIONS = 'notification.notifications';

    public const ORM_DEFAULT        = 'orm.default';
    public const ORM_ADAPTER        = 'orm.adapter';
    public const ORM_DRIVER         = 'orm.driver';
    public const ORM_QUERY          = 'orm.query';
    public const ORM_QUERY_BUILDER  = 'orm.queryBuilder';
    public const ORM_PERSISTER      = 'orm.persister';
    public const ORM_RETRIEVER      = 'orm.retriever';
    public const ORM_REPOSITORY     = 'orm.repository';
    public const ORM_CONNECTIONS    = 'orm.connections';
    public const ORM_MYSQL_ADAPTER  = 'orm.connections.mysql.adapter';
    public const ORM_MYSQL_DRIVER   = 'orm.connections.mysql.driver';
    public const ORM_MYSQL_HOST     = 'orm.connections.mysql.host';
    public const ORM_MYSQL_PORT     = 'orm.connections.mysql.post';
    public const ORM_MYSQL_DB       = 'orm.connections.mysql.db';
    public const ORM_MYSQL_CHARSET  = 'orm.connections.mysql.charset';
    public const ORM_MYSQL_USERNAME = 'orm.connections.mysql.username';
    public const ORM_MYSQL_PASSWORD = 'orm.connections.mysql.password';
    public const ORM_MIGRATIONS     = 'orm.migrations';

    public const PATH_PATTERNS = 'path.patterns';

    public const ROUTING_TRAILING_SLASH    = 'routing.trailingSlash';
    public const ROUTING_USE_ABSOLUTE_URLS = 'routing.useAbsoluteUrls';
    public const ROUTING_MIDDLEWARE        = 'routing.middleware';
    public const ROUTING_MIDDLEWARE_GROUPS = 'routing.middlewareGroups';
    public const ROUTING_USE_ANNOTATIONS   = 'routing.useAnnotations';
    public const ROUTING_CONTROLLERS       = 'routing.controllers';
    public const ROUTING_FILE_PATH         = 'routing.filePath';
    public const ROUTING_CACHE_FILE_PATH   = 'routing.cacheFilePath';
    public const ROUTING_USE_CACHE_FILE    = 'routing.useCache';
    public const ROUTING_CACHE             = 'routing.cache';

    public const SESSION_ID       = 'session.id';
    public const SESSION_NAME     = 'session.name';
    public const SESSION_ADAPTER  = 'session.adapter';
    public const SESSION_ADAPTERS = 'session.adapters';

    public const SMS_DEFAULT          = 'mail.default';
    public const SMS_ADAPTERS         = 'mail.adapters';
    public const SMS_DRIVERS          = 'mail.drivers';
    public const SMS_MESSENGERS       = 'mail.messengers';
    public const SMS_DEFAULT_MESSAGE  = 'mail.defaultMessage';
    public const SMS_MESSAGE_ADAPTERS = 'mail.messageAdapters';
    public const SMS_MESSAGES         = 'mail.messages';

    public const STORAGE_UPLOADS_DIR = 'storage.uploadsDir';
    public const STORAGE_LOGS_DIR    = 'storage.logsDir';

    public const VALIDATION_RULE      = 'validation.rule';
    public const VALIDATION_RULES     = 'validation.rules';
    public const VALIDATION_RULES_MAP = 'validation.rulesMap';

    public const VIEW_DIR                = 'view.dir';
    public const VIEW_ENGINE             = 'view.engine';
    public const VIEW_ENGINES            = 'view.engines';
    public const VIEW_PATHS              = 'view.paths';
    public const VIEW_DISKS              = 'view.disks';
    public const VIEW_PHP_FILE_EXTENSION = 'view.disks.php.fileExtension';
    public const VIEW_TWIG_COMPILED_DIR  = 'view.disks.twig.compiledDir';
    public const VIEW_TWIG_EXTENSIONS    = 'view.disks.twig.extensions';
}
