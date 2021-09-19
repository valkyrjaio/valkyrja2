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

namespace Valkyrja\Api\Config;

use Valkyrja\Api\Models\Json;
use Valkyrja\Api\Models\JsonData;
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
     */
    protected static array $envKeys = [
        CKP::JSON_MODEL      => EnvKey::API_JSON_MODEL,
        CKP::JSON_DATA_MODEL => EnvKey::API_JSON_DATA_MODEL,
    ];

    /**
     * The JSON model class.
     *
     * @var string
     */
    public string $jsonModel = Json::class;

    /**
     * The JSON data model class.
     *
     * @var string
     */
    public string $jsonDataModel = JsonData::class;
}
