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

namespace Valkyrja\Sms\Adapters;

use Valkyrja\Sms\Adapter as Contract;
use Valkyrja\Sms\Message;

/**
 * Class NullAdapter.
 *
 * @author Melech Mizrachi
 */
class NullAdapter implements Contract
{
    /**
     * The config.
     */
    protected array $config;

    /**
     * NullAdapter constructor.
     *
     * @param array $config The config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function send(Message $message): void
    {
    }
}
