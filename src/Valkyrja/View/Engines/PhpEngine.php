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

namespace Valkyrja\View\Engines;

use RuntimeException;
use Valkyrja\Support\Directory;
use Valkyrja\View\Config\Config;
use Valkyrja\View\Engine;
use Valkyrja\View\Exceptions\InvalidConfigPath;

use function explode;
use function extract;
use function implode;
use function ob_get_clean;
use function ob_start;
use function trim;

use const EXTR_SKIP;

/**
 * Class PhpEngine.
 *
 * @author Melech Mizrachi
 */
class PhpEngine implements Engine
{
    /**
     * The template directory.
     */
    protected string $dir;

    /**
     * The file extension.
     */
    protected string $fileExtension;

    /**
     * The view variables.
     *
     * @var array<string, mixed>
     */
    protected array $variables = [];

    /**
     * The paths.
     *
     * @var array<string, string>
     */
    protected array $paths;

    /**
     * PhpEngine constructor.
     *
     * @param Config|array{
     *     dir: string,
     *     engine: string,
     *     engines: array<string, class-string>,
     *     paths: array<string, string>,
     *     disks: array{php: array{fileExtension: string}}|array<string, array>
     * } $config The config
     */
    public function __construct(
        protected Config|array $config
    ) {
        $this->paths         = $config['paths'];
        $this->dir           = $config['dir'];
        $this->fileExtension = $config['disks']['php']['fileExtension'] ?? '.phtml';
    }

    /**
     * @inheritDoc
     */
    public function startRender(): void
    {
        ob_start();
    }

    /**
     * @inheritDoc
     */
    public function endRender(): string
    {
        return ob_get_clean();
    }

    /**
     * @inheritDoc
     */
    public function renderFile(string $name, array $variables = []): string
    {
        return $this->renderFullPath($this->getFullPath($name), $variables);
    }

    /**
     * Render a full file path.
     *
     * @param string $path      The file path
     * @param array  $variables [optional] The variables
     */
    protected function renderFullPath(string $path, array $variables = []): string
    {
        $this->startRender();
        $this->requirePath($path, $variables);

        return $this->endRender();
    }

    /**
     * Require a path to generate its contents with provided variables.
     *
     * @param string $path      The file path
     * @param array  $variables [optional] The variables
     */
    protected function requirePath(string $path, array $variables = []): void
    {
        if (is_file($path)) {
            extract($variables, EXTR_SKIP);

            require $path;

            return;
        }

        throw new RuntimeException("Path does not exist at $path");
    }

    /**
     * Get the full path for a given template name.
     *
     * @param string $template The template
     *
     * @throws InvalidConfigPath
     */
    protected function getFullPath(string $template): string
    {
        // If the first character of the template is an @ symbol
        // Then this is a template from a path in the config
        if (str_starts_with($template, '@')) {
            $explodeOn = Directory::DIRECTORY_SEPARATOR;
            $parts     = explode($explodeOn, $template);
            $path      = $this->paths[$parts[0]] ?? null;

            // If there is no path
            if ($path === null) {
                // Then throw an exception
                throw new InvalidConfigPath(
                    'Invalid path '
                    . $parts[0]
                    . ' specified for template '
                    . $template
                );
            }

            // Remove any trailing slashes
            $parts[0] = $explodeOn . trim($path, $explodeOn);

            $path = implode($explodeOn, $parts);
        } else {
            $path = $this->getDir($template);
        }

        return $path . $this->fileExtension;
    }

    /**
     * Get the template directory.
     *
     * @param string|null $path [optional] The path to append
     */
    protected function getDir(string $path = null): string
    {
        return $this->dir
            . ($path
                ? Directory::DIRECTORY_SEPARATOR . $path
                : '');
    }
}
