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

namespace Valkyrja\Container\Managers;

/**
 * Class ContextableContainer.
 *
 * @author Melech Mizrachi
 */
trait ContextableContainer
{
    /**
     * The context class or function name.
     *
     * @var class-string|string|null
     */
    protected ?string $context = null;

    /**
     * The context id.
     */
    protected ?string $contextId = null;

    /**
     * The context method name.
     */
    protected ?string $contextMember = null;

    /**
     * @inheritDoc
     */
    public function withContext(string $context, string $member = null): static
    {
        $contextContainer = new static($this->config, $this->debug);

        $contextContainer->context       = $context;
        $contextContainer->contextMember = $member;
        $contextContainer->contextId     = $this->getServiceId('', $context, $member);

        return $contextContainer;
    }

    /**
     * @inheritDoc
     */
    public function withoutContext(): static
    {
        $contextContainer = new static($this->config, $this->debug);

        $contextContainer->context       = null;
        $contextContainer->contextMember = null;
        $contextContainer->contextId     = null;

        return $contextContainer;
    }

    /**
     * Get a service id with optional context.
     *
     * @param class-string|string $serviceId The service id
     * @param string|null         $context   [optional] The context class or function name
     * @param string|null         $member    [optional] The context member name
     */
    protected function getServiceId(string $serviceId, string $context = null, string $member = null): string
    {
        if ($context === null) {
            return $serviceId;
        }

        return $serviceId . $context . ($member ?? '');
    }

    /**
     * Get a service id and ensure that it is published if it is provided.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function getServiceIdAndEnsurePublished(string $serviceId): string
    {
        // Get an aliased service id if it exists
        $serviceId = $this->getServiceIdInternal($serviceId);

        $this->publishUnpublishedProvided($serviceId);

        return $serviceId;
    }

    /**
     * Get the context service id.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function getServiceIdInternal(string $serviceId): string
    {
        $serviceId = $this->getAliasedServiceId($serviceId);

        if ($this->context === null) {
            return $serviceId;
        }

        // serviceId@context
        // serviceId@context::method
        return $serviceId . ($this->contextId ?? '');
    }
}
