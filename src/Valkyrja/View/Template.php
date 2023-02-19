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

namespace Valkyrja\View;

/**
 * Interface Template.
 *
 * @author Melech Mizrachi
 */
interface Template
{
    /**
     * Get the template name.
     */
    public function getName(): string;

    /**
     * Set the template name.
     *
     * @param string $name The name
     */
    public function setName(string $name): static;

    /**
     * Get the variables.
     *
     * @return array<string, mixed>
     */
    public function getVariables(): array;

    /**
     * Set the variables.
     *
     * @param array<string, mixed> $variables [optional] The variables to set
     */
    public function setVariables(array $variables = []): static;

    /**
     * Get a variable.
     *
     * @param string $key The variable key to set
     */
    public function getVariable(string $key): mixed;

    /**
     * Set a single variable.
     *
     * @param string $key   The variable key to set
     * @param mixed  $value The value to set
     */
    public function setVariable(string $key, mixed $value): static;

    /**
     * Escape a value for output.
     *
     * @param string|int|float $value The value to escape
     */
    public function escape(string|int|float $value): string;

    /**
     * Set the layout for the view template.
     *
     * @param string|null $layout [optional] The layout
     */
    public function setLayout(string $layout = null): static;

    /**
     * Set no layout for this view.
     */
    public function withoutLayout(): static;

    /**
     * Output a partial.
     *
     * @param string $partial   The partial
     * @param array  $variables [optional] The variables
     */
    public function getPartial(string $partial, array $variables = []): string;

    /**
     * Output a block.
     *
     * @param string $name The name of the block
     */
    public function getBlock(string $name): string;

    /**
     * Determine if a block exists.
     *
     * @param string $name The name of the block
     *
     * @return bool
     *              True if the block exists
     *              False if the block doesn't exist
     */
    public function hasBlock(string $name): bool;

    /**
     * Start a block.
     *
     * @param string $name The name of the block
     */
    public function startBlock(string $name): void;

    /**
     * End a block.
     */
    public function endBlock(): void;

    /**
     * Render the template.
     *
     * @param array $variables [optional] The variables to set
     */
    public function render(array $variables = []): string;

    /**
     * Get the view as a string.
     */
    public function __toString(): string;
}
