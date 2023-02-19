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

namespace Valkyrja\Http\Factories;

use InvalidArgumentException;
use Valkyrja\Http\Files\UploadedFile;

use function array_keys;
use function is_array;

/**
 * Abstract Class FileFactory.
 *
 * @author Melech Mizrachi
 */
abstract class FileFactory
{
    /**
     * Normalize uploaded files.
     * Transforms each value into an UploadedFileInterface instance, and ensures
     * that nested arrays are normalized.
     *
     * @throws InvalidArgumentException for unrecognized values
     *
     * @return UploadedFile[]
     */
    public static function normalizeFiles(array $files): array
    {
        $normalized = [];

        foreach ($files as $key => $value) {
            if ($value instanceof UploadedFile) {
                $normalized[$key] = $value;
                continue;
            }

            if (is_array($value) && isset($value['tmp_name'])) {
                $normalized[$key] = self::createUploadedFileFromSpec($value);
                continue;
            }

            if (is_array($value)) {
                $normalized[$key] = self::normalizeFiles($value);
                continue;
            }

            throw new InvalidArgumentException('Invalid value in files specification');
        }

        return $normalized;
    }

    /**
     * Create and return an UploadedFile instance from a $_FILES specification.
     * If the specification represents an array of values, this method will
     * delegate to normalizeNestedFileSpec() and return that return value.
     *
     * @param array $value $_FILES struct
     *
     * @throws InvalidArgumentException
     *
     * @return UploadedFile|UploadedFile[]
     */
    private static function createUploadedFileFromSpec(array $value): UploadedFile|array
    {
        if (is_array($value['tmp_name'])) {
            return self::normalizeNestedFileSpec($value);
        }

        return new UploadedFile(
            $value['size'],
            $value['error'],
            $value['tmp_name'],
            null,
            $value['name'],
            $value['type']
        );
    }

    /**
     * Normalize an array of file specifications.
     * Loops through all nested files and returns a normalized array of
     * UploadedFileInterface instances.
     *
     * @throws InvalidArgumentException
     *
     * @return UploadedFile[]
     */
    private static function normalizeNestedFileSpec(array $files = []): array
    {
        $normalizedFiles = [];

        foreach (array_keys($files['tmp_name']) as $key) {
            $spec                  = [
                'tmp_name' => $files['tmp_name'][$key],
                'size'     => $files['size'][$key],
                'error'    => $files['error'][$key],
                'name'     => $files['name'][$key],
                'type'     => $files['type'][$key],
            ];
            $normalizedFiles[$key] = self::createUploadedFileFromSpec($spec);
        }

        return $normalizedFiles;
    }
}
