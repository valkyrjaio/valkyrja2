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

namespace Valkyrja\Mail;

use Valkyrja\Manager\Message as Contract;

/**
 * Interface Message.
 *
 * @author Melech Mizrachi
 */
interface Message extends Contract
{
    /**
     * Get the from email.
     */
    public function getFromEmail(): string;

    /**
     * Get the from name.
     */
    public function getFromName(): string;

    /**
     * Set the mail's sender information.
     *
     * @param string $email The email
     * @param string $name  [optional] The name
     */
    public function setFrom(string $email, string $name = ''): static;

    /**
     * Get the recipients.
     */
    public function getRecipients(): array;

    /**
     * Add a recipient.
     *
     * @param string $email The email
     * @param string $name  [optional] The name
     */
    public function addRecipient(string $email, string $name = ''): static;

    /**
     * Get the reply to recipients.
     */
    public function getReplyToRecipients(): array;

    /**
     * Add a Reply-To recipient.
     *
     * @param string $email The email
     * @param string $name  [optional] The name
     */
    public function addReplyTo(string $email, string $name = ''): static;

    /**
     * Get the copy recipients.
     */
    public function getCopyRecipients(): array;

    /**
     * Add a copy (CC) recipient.
     *
     * @param string $email The email
     * @param string $name  [optional] The name
     */
    public function addCopyRecipient(string $email, string $name = ''): static;

    /**
     * Get the blind copy recipients.
     */
    public function getBlindCopyRecipients(): array;

    /**
     * Add a blind copy (BCC) recipient.
     *
     * @param string $email The email
     * @param string $name  [optional] The name
     */
    public function addBlindCopyRecipient(string $email, string $name = ''): static;

    /**
     * Get the attachments.
     */
    public function getAttachments(): array;

    /**
     * Add an attachment from the filesystem.
     *
     * @param string $path The path
     * @param string $name [optional] The name
     */
    public function addAttachment(string $path, string $name = ''): static;

    /**
     * Get the subject.
     */
    public function getSubject(): string;

    /**
     * Set the subject.
     *
     * @param string $subject The subject
     */
    public function setSubject(string $subject): static;

    /**
     * Get the body.
     */
    public function getBody(): string;

    /**
     * Set the body of the mail.
     *
     * @param string $body The body
     */
    public function setBody(string $body): static;

    /**
     * Get whether the message body is html.
     */
    public function isHtml(): bool;

    /**
     * Set whether the message body is html.
     *
     * @param bool $isHtml [optional] Whether the message body is html
     */
    public function setIsHtml(bool $isHtml = true): static;

    /**
     * Get the plain body.
     */
    public function getPlainBody(): ?string;

    /**
     * If sending html, add an alternative plain message body for clients without html support.
     *
     * @param string|null $plainBody The plain body
     */
    public function setPlainBody(string $plainBody = null): static;
}
