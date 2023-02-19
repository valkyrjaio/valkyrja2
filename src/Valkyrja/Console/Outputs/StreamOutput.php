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

namespace Valkyrja\Console\Outputs;

use InvalidArgumentException;
use RuntimeException;
use Valkyrja\Console\StreamOutput as StreamOutputContract;

use function fflush;
use function fopen;
use function fwrite;
use function get_resource_type;
use function is_resource;

use const PHP_EOL;

/**
 * Class StreamOutput.
 *
 * @author Melech Mizrachi
 */
class StreamOutput extends Output implements StreamOutputContract
{
    /**
     * The stream.
     *
     * @var resource
     */
    protected $stream;

    /**
     * Output constructor.
     *
     * @param resource|null $stream The resource to use as a stream
     */
    public function __construct($stream = null)
    {
        parent::__construct();

        // Set the resource
        $resource = $stream ?? fopen('php://stdout', 'wb');

        // If the resource isn't a valid resource or not a stream
        if (! is_resource($resource) || get_resource_type($resource) !== 'stream') {
            throw new InvalidArgumentException('Stream is not a valid stream resource.');
        }

        $this->stream = $resource;
    }

    /**
     * Write a message out to the console.
     *
     * @throws RuntimeException
     */
    protected function writeOut(string $message, bool $newLine): void
    {
        if (@fwrite($this->stream, $message) === false || ($newLine && (@fwrite($this->stream, PHP_EOL) === false))) {
            // should never happen
            throw new RuntimeException('Unable to write output.');
        }

        fflush($this->stream);
    }
}
