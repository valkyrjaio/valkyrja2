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

use Valkyrja\Model\Model;

/**
 * Interface Json.
 *
 * @author Melech Mizrachi
 */
interface Json extends Model
{
    /**
     * Get the message.
     */
    public function getMessage(): ?string;

    /**
     * Set the message.
     *
     * @param string|null $message [optional] The message
     */
    public function setMessage(string $message = null): static;

    /**
     * Get the errors.
     *
     * @return string[]
     */
    public function getErrors(): array;

    /**
     * Set the errors.
     *
     * @param string[] $errors The errors
     */
    public function setErrors(array $errors): static;

    /**
     * Set an error.
     *
     * @param string $error The error
     */
    public function setError(string $error): static;

    /**
     * Get the warnings.
     *
     * @return string[]
     */
    public function getWarnings(): array;

    /**
     * Set the warnings.
     *
     * @param string[] $warnings The warnings
     */
    public function setWarnings(array $warnings): static;

    /**
     * Set an warning.
     *
     * @param string $warning The warning
     */
    public function setWarning(string $warning): static;

    /**
     * Get the data.
     */
    public function getData(): ?array;

    /**
     * Set the data.
     */
    public function setData(array $data = null): static;

    /**
     * Get the status code.
     */
    public function getStatusCode(): int;

    /**
     * Set the status code.
     */
    public function setStatusCode(int $statusCode): static;

    /**
     * Get the status.
     */
    public function getStatus(): string;

    /**
     * Set the status.
     */
    public function setStatus(string $status): static;
}
