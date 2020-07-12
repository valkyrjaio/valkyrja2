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

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Valkyrja\Mail\Message as Contract;

/**
 * Class PHPMailerMessage.
 *
 * @author Melech Mizrachi
 */
class PHPMailerMessage implements Contract
{
    /**
     * The PHP Mailer.
     *
     * @var PHPMailer
     */
    protected PHPMailer $phpMailer;

    /**
     * NativeMail constructor.
     *
     * @param PHPMailer $phpMailer
     */
    public function __construct(PHPMailer $phpMailer)
    {
        $this->phpMailer = $phpMailer;
    }

    /**
     * Make a new message.
     *
     * @return static
     */
    public function make(): self
    {
        return new static(clone $this->phpMailer);
    }

    /**
     * Set the mail's sender information.
     *
     * @param string $address
     * @param string $name
     *
     * @throws Exception
     *
     * @return static
     */
    public function setFrom(string $address, string $name = ''): self
    {
        $this->phpMailer->setFrom($address, $name);

        return $this;
    }

    /**
     * Add a recipient.
     *
     * @param string $address
     * @param string $name
     *
     * @throws Exception
     *
     * @return static
     */
    public function addAddress(string $address, string $name = ''): self
    {
        $this->phpMailer->addAddress($address, $name);

        return $this;
    }

    /**
     * Add a Reply-To address.
     *
     * @param string $address
     * @param string $name
     *
     * @throws Exception
     *
     * @return static
     */
    public function addReplyTo(string $address, string $name = ''): self
    {
        $this->phpMailer->addReplyTo($address, $name);

        return $this;
    }

    /**
     * Add a copy recipient.
     *
     * @param string $address
     * @param string $name
     *
     * @throws Exception
     *
     * @return static
     */
    public function addCC(string $address, string $name = ''): self
    {
        $this->phpMailer->addCC($address, $name);

        return $this;
    }

    /**
     * Add a blind copy recipient.
     *
     * @param string $address
     * @param string $name
     *
     * @throws Exception
     *
     * @return static
     */
    public function addBCC(string $address, string $name = ''): self
    {
        $this->phpMailer->addBCC($address, $name);

        return $this;
    }

    /**
     * Add an attachment from the filesystem.
     *
     * @param string $path
     * @param string $name
     *
     * @throws Exception
     *
     * @return static
     */
    public function addAttachment(string $path, string $name = ''): self
    {
        $this->phpMailer->addAttachment($path, $name);

        return $this;
    }

    /**
     * Set the subject.
     *
     * @param string $subject The subject
     *
     * @return static
     */
    public function setSubject(string $subject): self
    {
        $this->phpMailer->Subject = $subject;

        return $this;
    }

    /**
     * Set the body of the mail.
     *
     * @param string $body The body
     *
     * @return static
     */
    public function setBody(string $body): self
    {
        $this->phpMailer->Body = $body;

        $this->phpMailer->isHTML(false);

        return $this;
    }

    /**
     * Set the html body of the mail.
     *
     * @param string $html The html
     *
     * @return static
     */
    public function setHtml(string $html): self
    {
        $this->phpMailer->Body = $html;

        $this->phpMailer->isHTML(true);

        return $this;
    }

    /**
     * If sending html, add an alternative plain message body for clients without html support.
     *
     * @param string $plainBody The plain body message
     *
     * @return static
     */
    public function setPlainBody(string $plainBody): self
    {
        $this->phpMailer->AltBody = $plainBody;

        return $this;
    }

    /**
     * Send the mail.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function send(): bool
    {
        return $this->phpMailer->send();
    }
}
