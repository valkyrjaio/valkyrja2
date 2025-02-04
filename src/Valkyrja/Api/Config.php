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

namespace Valkyrja\Api;

use Valkyrja\Api\Model\Contract\Json;
use Valkyrja\Api\Model\Contract\JsonData;
use Valkyrja\Application\Constant\EnvKey;
use Valkyrja\Config\Config as Model;
use Valkyrja\Config\Constant\ConfigKeyPart as CKP;

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
        CKP::JSON_MODEL      => EnvKey::API_JSON_MODEL,
        CKP::JSON_DATA_MODEL => EnvKey::API_JSON_DATA_MODEL,
    ];

    /**
     * The JSON model class.
     *
     * @var class-string<Json>
     */
    public string $jsonModel;

    /**
     * The JSON data model class.
     *
     * @var class-string<JsonData>
     */
    public string $jsonDataModel;
}
