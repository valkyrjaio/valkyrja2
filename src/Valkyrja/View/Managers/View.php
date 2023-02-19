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

namespace Valkyrja\View\Managers;

use Valkyrja\Container\Container;
use Valkyrja\View\Config\Config;
use Valkyrja\View\Engine;
use Valkyrja\View\Factory;
use Valkyrja\View\Template;
use Valkyrja\View\View as Contract;

/**
 * Class View.
 *
 * @author Melech Mizrachi
 */
class View implements Contract
{
    /**
     * The engines.
     *
     * @var Engine[]
     */
    protected static array $engines = [];

    /**
     * The templates.
     *
     * @var Template[]
     */
    protected static array $templates = [];

    /**
     * The body content template.
     */
    protected string $template = 'index';

    /**
     * The fully qualified template path.
     */
    protected string $templatePath;

    /**
     * The view variables.
     */
    protected array $variables = [];

    /**
     * The engines.
     *
     * @var array<string, class-string<Engine>>
     */
    protected array $enginesConfig;

    /**
     * The default engine.
     */
    protected string $engine;

    /**
     * View constructor.
     *
     * @param Container $container The container
     * @param Config|array{
     *     dir: string,
     *     engine: string,
     *     engines: array<string, class-string>,
     *     paths: array<string, string>,
     *     disks: array<string, array>
     * }                $config    The config
     */
    public function __construct(
        protected Container $container,
        protected Factory $factory,
        protected Config|array $config
    ) {
        $this->engine        = $config['engine'];
        $this->enginesConfig = $config['engines'];
    }

    /**
     * @inheritDoc
     */
    public function createTemplate(string $name, array $variables = [], string $engine = null): Template
    {
        return $this->factory->getTemplate($this->getEngine($engine), $name, $variables);
    }

    /**
     * @inheritDoc
     */
    public function getEngine(string $name = null): Engine
    {
        $name ??= $this->engine;

        return self::$engines[$name]
            ??= $this->factory->getEngine($this->enginesConfig[$name]);
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, array $variables = []): string
    {
        return $this->createTemplate($name, $variables)->render();
    }
}
