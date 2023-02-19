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

namespace Valkyrja\Mail\Messages;

use Valkyrja\Mail\Message as Contract;

/**
 * Class Message.
 *
 * @author Melech Mizrachi
 */
class Message implements Contract
{
    /**
     * The from email.
     */
    protected string $fromEmail = '';

    /**
     * The from name.
     */
    protected string $fromName = '';

    /**
     * The recipients.
     *
     * @var array[]
     */
    protected array $recipients = [];

    /**
     * The reply to recipients.
     *
     * @var array[]
     */
    protected array $replyToRecipients = [];

    /**
     * The copy recipients.
     *
     * @var array[]
     */
    protected array $copyRecipients = [];

    /**
     * The blind copy recipients.
     *
     * @var array[]
     */
    protected array $blindCopyRecipients = [];

    /**
     * The attachments.
     *
     * @var array[]
     */
    protected array $attachments = [];

    /**
     * The subject.
     */
    protected string $subject = '';

    /**
     * The body.
     */
    protected string $body = '';

    /**
     * Whether the message body is html.
     */
    protected bool $isHtml = false;

    /**
     * The plain body.
     */
    protected ?string $plainBody = null;

    /**
     * @inheritDoc
     */
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    /**
     * @inheritDoc
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * @inheritDoc
     */
    public function setFrom(string $email, string $name = ''): static
    {
        $this->fromEmail = $email;
        $this->fromName  = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @inheritDoc
     */
    public function addRecipient(string $email, string $name = ''): static
    {
        $this->recipients[] = [
            'email' => $email,
            'name'  => $name,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getReplyToRecipients(): array
    {
        return $this->replyToRecipients;
    }

    /**
     * @inheritDoc
     */
    public function addReplyTo(string $email, string $name = ''): static
    {
        $this->replyToRecipients[] = [
            'email' => $email,
            'name'  => $name,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCopyRecipients(): array
    {
        return $this->copyRecipients;
    }

    /**
     * @inheritDoc
     */
    public function addCopyRecipient(string $email, string $name = ''): static
    {
        $this->copyRecipients[] = [
            'email' => $email,
            'name'  => $name,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBlindCopyRecipients(): array
    {
        return $this->blindCopyRecipients;
    }

    /**
     * @inheritDoc
     */
    public function addBlindCopyRecipient(string $email, string $name = ''): static
    {
        $this->blindCopyRecipients[] = [
            'email' => $email,
            'name'  => $name,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @inheritDoc
     */
    public function addAttachment(string $path, string $name = ''): static
    {
        $this->attachments[] = [
            'path' => $path,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @inheritDoc
     */
    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isHtml(): bool
    {
        return $this->isHtml;
    }

    /**
     * @inheritDoc
     */
    public function setIsHtml(bool $isHtml = true): static
    {
        $this->isHtml = $isHtml;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPlainBody(): ?string
    {
        return $this->plainBody;
    }

    /**
     * @inheritDoc
     */
    public function setPlainBody(string $plainBody = null): static
    {
        $this->plainBody = $plainBody;

        return $this;
    }
}
