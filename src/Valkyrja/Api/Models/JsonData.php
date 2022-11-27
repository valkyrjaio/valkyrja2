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

use Valkyrja\Api\JsonData as Contract;
use Valkyrja\Model\Classes\Model;

/**
 * Class JsonData.
 *
 * @author Melech Mizrachi
 */
class JsonData extends Model implements Contract
{
    /**
     * The item.
     *
     * @var object|null
     */
    public ?object $item = null;

    /**
     * The item key.
     *
     * @var string
     */
    public string $itemKey = 'item';

    /**
     * The items.
     *
     * @var object[]|null
     */
    public ?array $items = null;

    /**
     * The items key
     *
     * @var string
     */
    public string $itemsKey = 'items';

    /**
     * The total.
     *
     * @var int|null
     */
    public ?int $total = null;

    /**
     * The message.
     *
     * @var string|null
     */
    public ?string $message = null;

    /**
     * The messages.
     *
     * @var array|null
     */
    public ?array $messages = null;

    /**
     * The data.
     *
     * @var array|null
     */
    public ?array $data = null;

    /**
     * @inheritDoc
     */
    public function getItem(): ?object
    {
        return $this->item;
    }

    /**
     * @inheritDoc
     */
    public function setItem(object $item = null): self
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getItemKey(): string
    {
        return $this->itemKey;
    }

    /**
     * @inheritDoc
     */
    public function setItemKey(string $itemKey): self
    {
        $this->itemKey = $itemKey;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getItems(): ?array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function setItems(array $items = null): self
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getItemsKey(): string
    {
        return $this->itemsKey;
    }

    /**
     * @inheritDoc
     */
    public function setItemsKey(string $itemsKey): self
    {
        $this->itemsKey = $itemsKey;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function setTotal(int $total = null): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessages(): ?array
    {
        return $this->messages;
    }

    /**
     * @inheritDoc
     */
    public function setMessages(array $messages = null): self
    {
        $this->messages = $messages;

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
    public function setData(array $data = null): self
    {
        $this->data = $data;

        return $this;
    }

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
    public function setMessage(string $message = null): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function asArray(string ...$properties): array
    {
        $data = $this->data ?? [];

        if ($this->messages) {
            $data['messages'] = $this->messages;
        }

        if ($this->item) {
            $data[$this->itemKey] = $this->item;
        }

        if ($this->items) {
            $data[$this->itemsKey] = $this->items;
        }

        if ($this->total !== null) {
            $data['total'] = $this->total;
        }

        return $data;
    }
}
