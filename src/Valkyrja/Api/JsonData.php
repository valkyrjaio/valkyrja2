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
 * Interface JsonData.
 *
 * @author Melech Mizrachi
 */
interface JsonData extends Model
{
    /**
     * Get the item.
     */
    public function getItem(): ?object;

    /**
     * Set the item.
     */
    public function setItem(object $item = null): static;

    /**
     * Get the item key.
     */
    public function getItemKey(): string;

    /**
     * Set the item key.
     */
    public function setItemKey(string $itemKey): static;

    /**
     * Get the items.
     */
    public function getItems(): ?array;

    /**
     * Set the items.
     */
    public function setItems(array $items = null): static;

    /**
     * Get the items key.
     */
    public function getItemsKey(): string;

    /**
     * Set the items key.
     */
    public function setItemsKey(string $itemsKey): static;

    /**
     * Get the total.
     */
    public function getTotal(): ?int;

    /**
     * Set the total.
     */
    public function setTotal(int $total = null): static;

    /**
     * Get the messages.
     */
    public function getMessages(): ?array;

    /**
     * Set the messages.
     */
    public function setMessages(array $messages = null): static;

    /**
     * Get the data.
     */
    public function getData(): ?array;

    /**
     * Set the data.
     */
    public function setData(array $data = null): static;
}
