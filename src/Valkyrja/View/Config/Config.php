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

namespace Valkyrja\View\Config;

use Valkyrja\Config\Config as Model;
use Valkyrja\Config\Constants\ConfigKeyPart as CKP;
use Valkyrja\Config\Constants\EnvKey;

/**
 * Class Config.
 *
 * @author Melech Mizrachi
 */
class Config extends Model
{
    /**
     * @inheritDoc
     *
     * @var array<string, string>
     */
    protected static array $envKeys = [
        CKP::DIR     => EnvKey::VIEW_DIR,
        CKP::ENGINE  => EnvKey::VIEW_ENGINE,
        CKP::ENGINES => EnvKey::VIEW_ENGINES,
        CKP::PATHS   => EnvKey::VIEW_PATHS,
        CKP::DISKS   => EnvKey::VIEW_DISKS,
    ];

    /**
     * The dir.
     */
    public string $dir;

    /**
     * The default engine.
     */
    public string $engine;

    /**
     * The engines.
     *
     * @var array<string, class-string>
     */
    public array $engines;

    /**
     * The paths.
     *
     * @example
     * <code>
     *      [
     *         '@path' => '/some/path/on/disk',
     *      ]
     * </code>
     * Then we can do:
     * <code>
     *      view('@path/template');
     *      $view->layout('@path/layout');
     *      $view->partial('@path/partials/partial');
     * </code>
     *
     * @var array<string, string>
     */
    public array $paths;

    /**
     * The disks.
     *
     * @var array<string, array>
     */
    public array $disks;
}
