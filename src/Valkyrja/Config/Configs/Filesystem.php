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

namespace Valkyrja\Config\Configs;

use Valkyrja\Config\Configs\Filesystem\Disks;
use Valkyrja\Config\Enums\ConfigKeyPart as CKP;
use Valkyrja\Config\Enums\EnvKey;
use Valkyrja\Config\Models\Config as Model;
use Valkyrja\Filesystem\Enums\Config;

/**
 * Class Filesystem.
 *
 * @author Melech Mizrachi
 */
class Filesystem extends Model
{
    public string $default  = CKP::LOCAL;
    public array  $adapters = [];
    public Disks  $disks;

    /**
     * Filesystem constructor.
     */
    public function __construct()
    {
        $this->default  = (string) env(EnvKey::FILESYSTEM_DEFAULT, $this->default);
        $this->adapters = (array) env(EnvKey::FILESYSTEM_ADAPTERS, array_merge(Config::ADAPTERS, $this->adapters));

        $this->setDisks();
    }

    /**
     * Set disks.
     *
     * @return void
     */
    protected function setDisks(): void
    {
        $this->disks = new Disks();
    }
}
