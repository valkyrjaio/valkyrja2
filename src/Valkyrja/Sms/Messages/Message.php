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

namespace Valkyrja\Sms\Messages;

use Valkyrja\Sms\Message as Contract;

/**
 * Class Message.
 *
 * @author Melech Mizrachi
 */
class Message implements Contract
{
    /**
     * The message to.
     */
    protected string $to;

    /**
     * The message from.
     */
    protected string $from;

    /**
     * The message text.
     */
    protected string $text;

    /**
     * Whether the text is unicode.
     */
    protected bool $isUnicode = true;

    /**
     * @inheritDoc
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @inheritDoc
     */
    public function setTo(string $to): static
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @inheritDoc
     */
    public function setFrom(string $from): static
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @inheritDoc
     */
    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isUnicode(): bool
    {
        return $this->isUnicode;
    }

    /**
     * @inheritDoc
     */
    public function setIsUnicode(bool $isUnicode = true): static
    {
        $this->isUnicode = $isUnicode;

        return $this;
    }
}
