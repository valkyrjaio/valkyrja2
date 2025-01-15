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

namespace Valkyrja\Mail\Adapter;

use Valkyrja\Mail\Adapter\Contract\Adapter as Contract;
use Valkyrja\Mail\Message\Contract\Message;

/**
 * Class NullAdapter.
 *
 * @author Melech Mizrachi
 */
class NullAdapter implements Contract
{
    /**
     * NullAdapter constructor.
     *
     * @param array<string, mixed> $config The config
     */
    public function __construct(
        protected array $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function send(Message $message): void
    {
    }
}
