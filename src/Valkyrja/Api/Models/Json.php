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

namespace Valkyrja\Api\Models;

use Valkyrja\Api\Constants\Status;
use Valkyrja\Api\Json as Contract;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Model\Models\Model;

/**
 * Class Json.
 *
 * @author Melech Mizrachi
 */
class Json extends Model implements Contract
{
    /**
     * The message.
     */
    public ?string $message = null;

    /**
     * The data.
     */
    public ?array $data = null;

    /**
     * The errors.
     *
     * @var string[]
     */
    public array $errors = [];

    /**
     * The warnings.
     *
     * @var string[]
     */
    public array $warnings = [];

    /**
     * The status code.
     */
    public int $statusCode = StatusCode::OK;

    /**
     * The status.
     */
    public string $status = Status::SUCCESS;

    /**
     * @inheritDoc
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message = null): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    public function setErrors(array $errors): static
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setError(string $error): static
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @inheritDoc
     */
    public function setWarnings(array $warnings): static
    {
        $this->warnings = $warnings;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setWarning(string $warning): static
    {
        $this->warnings[] = $warning;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data = null): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $array = [
            'data'       => $this->data,
            'statusCode' => $this->statusCode,
            'status'     => $this->status,
        ];

        if ($this->message) {
            $array['message'] = $this->message;
        }

        return $array;
    }
}
