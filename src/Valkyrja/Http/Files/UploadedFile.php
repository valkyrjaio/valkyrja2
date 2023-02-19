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

namespace Valkyrja\Http\Files;

use InvalidArgumentException;
use RuntimeException;
use Valkyrja\Http\Exceptions\InvalidStream;
use Valkyrja\Http\Exceptions\UploadedFileException;
use Valkyrja\Http\Stream;
use Valkyrja\Http\Streams\Stream as HttpStream;
use Valkyrja\Http\UploadedFile as Contract;

use function dirname;
use function fclose;
use function fopen;
use function fwrite;
use function is_dir;
use function is_writable;
use function move_uploaded_file;
use function sprintf;

use const PHP_SAPI;
use const UPLOAD_ERR_EXTENSION;
use const UPLOAD_ERR_OK;

/**
 * Class UploadedFile.
 *
 * @author Melech Mizrachi
 */
class UploadedFile implements Contract
{
    /**
     * Whether the file has been moved yet.
     */
    protected bool $moved = false;

    /**
     * NativeUploadedFile constructor.
     *
     * @param int         $size        The file size
     * @param int         $errorStatus The error status
     * @param string|null $file        [optional] The file
     * @param Stream|null $stream      [optional] The stream
     * @param string|null $fileName    [optional] The file name
     * @param string|null $mediaType   [optional] The file media type
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected int $size,
        protected int $errorStatus,
        protected string|null $file = null,
        protected Stream|null $stream = null,
        protected string|null $fileName = null,
        protected string|null $mediaType = null
    ) {
        // If the error is less than the lowest valued UPLOAD_ERR_* constant
        // Or the error is greater than the highest valued UPLOAD_ERR_* constant
        if ($errorStatus < UPLOAD_ERR_OK || $errorStatus > UPLOAD_ERR_EXTENSION) {
            // Throw an invalid argument exception for the error status
            throw new InvalidArgumentException(
                'Invalid error status for UploadedFile;  must be an UPLOAD_ERR_* constant value.'
            );
        }

        // If the file is not set and the stream is not set
        if ($file === null && $stream === null) {
            // Throw an invalid argument exception as on or the other is required
            throw new InvalidArgumentException(
                'Either one of file or stream are required. Neither passed as arguments.'
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getStream(): Stream
    {
        // If the error status is not OK
        if ($this->errorStatus !== UPLOAD_ERR_OK) {
            // Throw a runtime exception as there's been an uploaded file error
            throw new UploadedFileException('Cannot retrieve stream due to upload error');
        }

        // If the file has already been moved
        if ($this->moved) {
            // Throw a runtime exception as subsequent moves are not allowed in PSR-7
            throw new UploadedFileException('Cannot retrieve stream after it has already been moved');
        }

        // If the stream has been set
        if ($this->stream !== null) {
            // Return the stream
            return $this->stream;
        }

        if ($this->file === null) {
            throw new InvalidArgumentException('Either one of file or stream are required. Neither exists.');
        }

        // Set the stream as a new native stream
        $this->stream = new HttpStream($this->file);

        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function moveTo(string $targetPath): void
    {
        // If the error status is not OK
        if ($this->errorStatus !== UPLOAD_ERR_OK) {
            // Throw a runtime exception as there's been an uploaded file error
            throw new UploadedFileException('Cannot retrieve stream due to upload error');
        }

        // If the file has already been moved
        if ($this->moved) {
            // Throw a runtime exception as subsequent moves are not allowed
            // in PSR-7
            throw new UploadedFileException('Cannot move file after it has already been moved');
        }

        $targetDirectory = dirname($targetPath);

        // If the target directory is not a directory
        // or the target directory is not writable
        if (! is_dir($targetDirectory) || ! is_writable($targetDirectory)) {
            // Throw a runtime exception
            throw new UploadedFileException(
                sprintf('The target directory `%s` does not exists or is not writable', $targetDirectory)
            );
        }

        $sapi = PHP_SAPI;

        // If the PHP_SAPI value is empty
        // or there is no file
        // or the PHP_SAPI value is set to a CLI environment
        if (empty($sapi) || ! $this->file || str_starts_with($sapi, 'cli')) {
            // Non-SAPI environment, or no filename present
            $this->writeStream($targetPath);
        }
        // Otherwise try to use the move_uploaded_file function
        // and if the move_uploaded_file function call failed
        elseif (! move_uploaded_file($this->file, $targetPath)) {
            // Throw a runtime exception
            throw new UploadedFileException('Error occurred while moving uploaded file');
        }

        $this->moved = true;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getError(): int
    {
        return $this->errorStatus;
    }

    /**
     * @inheritDoc
     */
    public function getClientFilename(): ?string
    {
        return $this->fileName;
    }

    /**
     * @inheritDoc
     */
    public function getClientMediaType(): ?string
    {
        return $this->mediaType;
    }

    /**
     * Write the stream to a path.
     *
     * @param string $path The path to write the stream to
     *
     * @throws InvalidStream
     * @throws RuntimeException
     */
    protected function writeStream(string $path): void
    {
        // Attempt to open the path specified
        $handle = fopen($path, 'wb+');

        // If the handler failed to open
        if ($handle === false) {
            // Throw a runtime exception
            throw new UploadedFileException('Unable to write to designated path');
        }

        // Get the stream
        $stream = $this->getStream();
        // Rewind the stream
        $stream->rewind();

        // While the end of file hasn't been reached
        while (! $stream->eof()) {
            // Write the stream's contents to the handler
            fwrite($handle, $stream->read(4096));
        }

        // Close the file path
        fclose($handle);
    }
}
