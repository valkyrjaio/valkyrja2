<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Filesystem;

use Aws\S3\S3Client;
use InvalidArgumentException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem as FlySystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\RootViolationException;
use LogicException;
use Valkyrja\Application\Application;
use Valkyrja\Config\Enums\ConfigKeyPart;
use Valkyrja\Filesystem\Enums\Visibility;
use Valkyrja\Support\Providers\Provides;

/**
 * Class Filesystem.
 *
 * @author Melech Mizrachi
 */
class FlyFilesystem implements Filesystem
{
    use Provides;

    /**
     * The application.
     *
     * @var \Valkyrja\Application\Application
     */
    protected Application $app;

    /**
     * The Fly Filesystem.
     *
     * @var FlySystem
     */
    protected FlySystem $flySystem;

    /**
     * The adapters.
     *
     * @var AdapterInterface[]
     */
    protected static array $adapters = [];

    /**
     * FlyFilesystem constructor.
     *
     * @param \Valkyrja\Application\Application $application The application
     * @param FilesystemInterface|null          $flySystem   [optional] The FlyFilesystem
     */
    public function __construct(Application $application, FilesystemInterface $flySystem = null)
    {
        $this->app       = $application;
        $this->flySystem = $flySystem
            ?? new FlySystem(
                $this->flyAdapter($this->app->config()[ConfigKeyPart::FILESYSTEM][ConfigKeyPart::DEFAULT])
            );
    }

    /**
     * Determine whether a path exists.
     *
     * @param string $path The path
     *
     * @return bool
     */
    public function exists(string $path): bool
    {
        return $this->flySystem->has($path);
    }

    /**
     * Read a file.
     *
     * @param string $path The path
     *
     * @throws FileNotFoundException
     *
     * @return string|null The contents or null on failure
     */
    public function read(string $path): ?string
    {
        $read = $this->flySystem->read($path);

        return false !== $read ? $read : null;
    }

    /**
     * Write a file.
     *
     * @param string $path     The path
     * @param string $contents The contents
     *
     * @throws FileExistsException
     *
     * @return bool
     */
    public function write(string $path, string $contents): bool
    {
        return $this->flySystem->write($path, $contents);
    }

    /**
     * Write a file with a stream.
     *
     * @param string   $path     The path
     * @param resource $resource The resource
     *
     * @throws FileExistsException
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function writeStream(string $path, $resource): bool
    {
        return $this->flySystem->writeStream($path, $resource);
    }

    /**
     * Update an existing file.
     *
     * @param string $path     The path
     * @param string $contents The contents
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function update(string $path, string $contents): bool
    {
        return $this->flySystem->update($path, $contents);
    }

    /**
     * Update an existing file with a stream.
     *
     * @param string   $path     The path
     * @param resource $resource The resource
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function updateStream(string $path, $resource): bool
    {
        return $this->flySystem->updateStream($path, $resource);
    }

    /**
     * Write a file or update a file depending on existence.
     *
     * @param string $path     The path
     * @param string $contents The contents
     *
     * @return bool
     */
    public function put(string $path, string $contents): bool
    {
        return $this->flySystem->put($path, $contents);
    }

    /**
     * Write a file or update a file depending on existence with a stream.
     *
     * @param string   $path     The path
     * @param resource $resource The resource
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function putStream(string $path, $resource): bool
    {
        return $this->flySystem->putStream($path, $resource);
    }

    /**
     * Rename a file.
     *
     * @param string $path    The existing path
     * @param string $newPath The new path
     *
     * @throws FileNotFoundException
     * @throws FileExistsException
     *
     * @return bool
     */
    public function rename(string $path, string $newPath): bool
    {
        return $this->flySystem->rename($path, $newPath);
    }

    /**
     * Copy a file.
     *
     * @param string $path    The existing path
     * @param string $newPath The new path
     *
     * @throws FileNotFoundException
     * @throws FileExistsException
     *
     * @return bool
     */
    public function copy(string $path, string $newPath): bool
    {
        return $this->flySystem->copy($path, $newPath);
    }

    /**
     * Delete a file.
     *
     * @param string $path The path
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function delete(string $path): bool
    {
        return $this->flySystem->delete($path);
    }

    /**
     * Get a file's meta data.
     *
     * @param string $path The path
     *
     * @throws FileNotFoundException
     *
     * @return array|null An array of meta data or null on failure
     */
    public function metadata(string $path): ?array
    {
        $metadata = $this->flySystem->getMetadata($path);

        return false !== $metadata ? $metadata : null;
    }

    /**
     * Get a file's mime type.
     *
     * @param string $path The path
     *
     * @throws FileNotFoundException
     *
     * @return string|null The mime type or null on failure
     */
    public function mimetype(string $path): ?string
    {
        $mimetype = $this->flySystem->getMimetype($path);

        return false !== $mimetype ? $mimetype : null;
    }

    /**
     * Get a file's size.
     *
     * @param string $path The path
     *
     * @throws FileNotFoundException
     *
     * @return int|null The size in bytes or null on failure
     */
    public function size(string $path): ?int
    {
        $size = $this->flySystem->getSize($path);

        return false !== $size ? $size : null;
    }

    /**
     * Get a file's timestamp.
     *
     * @param string $path The path
     *
     * @throws FileNotFoundException
     *
     * @return int|null The timestamp or null on failure
     */
    public function timestamp(string $path): ?int
    {
        $timestamp = $this->flySystem->getTimestamp($path);

        return false !== $timestamp ? (int) $timestamp : null;
    }

    /**
     * Get a file's visibility.
     *
     * @param string $path The path
     *
     * @throws FileNotFoundException
     *
     * @return string|null The visibility ('public' or 'private') or null on failure
     */
    public function visibility(string $path): ?string
    {
        $visibility = $this->flySystem->getVisibility($path);

        return false !== $visibility ? $visibility : null;
    }

    /**
     * Set a file's visibility.
     *
     * @param string     $path       The path
     * @param Visibility $visibility The visibility
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function setVisibility(string $path, Visibility $visibility): bool
    {
        return $this->flySystem->setVisibility($path, $visibility->getValue());
    }

    /**
     * Set a file's visibility to public.
     *
     * @param string $path The path
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function setVisibilityPublic(string $path): bool
    {
        return $this->flySystem->setVisibility($path, Visibility::PUBLIC);
    }

    /**
     * Set a file's visibility to private.
     *
     * @param string $path The path
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function setVisibilityPrivate(string $path): bool
    {
        return $this->flySystem->setVisibility($path, Visibility::PRIVATE);
    }

    /**
     * Create a new directory.
     *
     * @param string $path The path
     *
     * @return bool
     */
    public function createDir(string $path): bool
    {
        return $this->flySystem->createDir($path);
    }

    /**
     * Delete a directory.
     *
     * @param string $path The path
     *
     * @throws RootViolationException
     *
     * @return bool
     */
    public function deleteDir(string $path): bool
    {
        return $this->flySystem->deleteDir($path);
    }

    /**
     * List the contents of a directory.
     *
     * @param string $directory [optional] The directory
     * @param bool   $recursive [optional] Whether to recurse through the directory
     *
     * @return array
     */
    public function listContents(string $directory = null, bool $recursive = false): array
    {
        return $this->flySystem->listContents($directory ?? '', $recursive);
    }

    /**
     * Get a filesystem for an adapter.
     *
     * @param string $adapter The adapter
     *
     * @return Filesystem
     */
    public function adapter(string $adapter): Filesystem
    {
        $flyAdapter = $this->{$adapter . 'Adapter'}();

        return new static($flyAdapter);
    }

    /**
     * Get a flysystem abstract adapter.
     *
     * @param string $adapter The adapter
     *
     * @return AbstractAdapter
     */
    protected function flyAdapter(string $adapter): AbstractAdapter
    {
        return $this->{$adapter . 'Adapter'}();
    }

    /**
     * Get the local filesystem.
     *
     * @throws LogicException
     *
     * @return Filesystem
     */
    public function local(): Filesystem
    {
        return new static($this->app, new FlySystem($this->localAdapter()));
    }

    /**
     * Get the local flysystem adapter.
     *
     * @throws LogicException
     *
     * @return Local
     */
    protected function localAdapter(): Local
    {
        return self::$adapters[ConfigKeyPart::LOCAL]
            ?? self::$adapters[ConfigKeyPart::LOCAL] = new Local(
                $this->app->config(
                )[ConfigKeyPart::FILESYSTEM][ConfigKeyPart::ADAPTERS][ConfigKeyPart::LOCAL][ConfigKeyPart::DIR]
            );
    }

    /**
     * Get the s3 filesystem.
     *
     * @throws InvalidArgumentException
     *
     * @return Filesystem
     */
    public function s3(): Filesystem
    {
        return new static($this->app, new FlySystem($this->s3Adapter()));
    }

    /**
     * Get the s3 flysystem adapter.
     *
     * @throws InvalidArgumentException
     *
     * @return AwsS3Adapter
     */
    protected function s3Adapter(): AwsS3Adapter
    {
        if (isset(self::$adapters[ConfigKeyPart::S3])) {
            return self::$adapters[ConfigKeyPart::S3];
        }

        $config       = $this->app->config()[ConfigKeyPart::FILESYSTEM][ConfigKeyPart::ADAPTERS][ConfigKeyPart::S3];
        $clientConfig = [
            'credentials' => [
                'key'    => $config[ConfigKeyPart::KEY],
                'secret' => $config[ConfigKeyPart::SECRET],
            ],
            'region'      => $config[ConfigKeyPart::REGION],
            'version'     => $config[ConfigKeyPart::VERSION],
        ];

        self::$adapters['s3'] = new AwsS3Adapter(
            new S3Client($clientConfig), $config[ConfigKeyPart::BUCKET], $config[ConfigKeyPart::DIR]
        );

        return self::$adapters[ConfigKeyPart::S3];
    }

    /**
     * The items provided by this provider.
     *
     * @return array
     */
    public static function provides(): array
    {
        return [
            Filesystem::class,
        ];
    }

    /**
     * Publish the provider.
     *
     * @param \Valkyrja\Application\Application $app The application
     *
     * @return void
     */
    public static function publish(Application $app): void
    {
        $app->container()->singleton(
            Filesystem::class,
            new static($app)
        );
    }
}
