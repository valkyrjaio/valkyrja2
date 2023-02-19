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

namespace Valkyrja\Tests\Unit\Container;

use AssertionError;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Valkyrja\Container\Config\Container as Config;
use Valkyrja\Container\Managers\Container;
use Valkyrja\Dispatcher\Dispatcher;
use Valkyrja\Tests\Classes\Container\Service;
use Valkyrja\Tests\Classes\Container\Singleton;
use Valkyrja\Tests\Traits\ExpectErrorTrait;

/**
 * Test the container service.
 *
 * @author Melech Mizrachi
 */
class ContainerTest extends TestCase
{
    use ExpectErrorTrait;

    /**
     * The class to test with.
     */
    protected Container $container;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container(new Config(setup: true));
    }

    public function testInvalidBind(): void
    {
        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage('assert(is_a($service, Service::class, true))');

        $container = $this->container;

        $container->bind(self::class, self::class);
    }

    public function testBind(): void
    {
        $container = $this->container;
        $serviceId = Service::class;

        $container->bind($serviceId, $serviceId);

        $this->assertTrue($container->has($serviceId));
        $this->assertTrue($container->isService($serviceId));

        $this->assertFalse($container->isAlias($serviceId));
        $this->assertFalse($container->isClosure($serviceId));
        $this->assertFalse($container->isSingleton($serviceId));
        $this->assertFalse($container->isProvided($serviceId));
        $this->assertFalse($container->isPublished($serviceId));

        $this->assertInstanceOf($serviceId, $service = $container->get($serviceId));
        // A bound service should return a new instance each time it is retrieved
        $this->assertNotSame($service, $container->get($serviceId));

        $this->assertInstanceOf($serviceId, $container->getService($serviceId));
        $this->assertNotSame($service, $container->getService($serviceId));
    }

    public function testBindAlias(): void
    {
        $container = $this->container;
        $serviceId = Service::class;
        $alias     = 'alias';

        $container->bindAlias($alias, $serviceId);

        $this->assertTrue($container->has($alias));
        $this->assertTrue($container->isAlias($alias));
        $this->assertTrue($container->isService($alias));

        $this->assertFalse($container->isClosure($serviceId));
        $this->assertFalse($container->isSingleton($serviceId));
        $this->assertFalse($container->isProvided($serviceId));
        $this->assertFalse($container->isPublished($serviceId));

        $this->assertInstanceOf($serviceId, $service = $container->get($alias));
        // A bound service should return a new instance each time it is retrieved
        $this->assertNotSame($service, $container->get($alias));

        $this->assertInstanceOf($serviceId, $container->getService($alias));
        $this->assertNotSame($service, $container->getService($alias));
    }

    public function testBindSingleton(): void
    {
        $container = $this->container;
        $serviceId = Singleton::class;

        $container->bindSingleton($serviceId, $serviceId);

        $this->assertTrue($container->has($serviceId));
        $this->assertTrue($container->isSingleton($serviceId));
        // A singleton is a service when bound
        $this->assertTrue($container->isService($serviceId));

        $this->assertFalse($container->isAlias($serviceId));
        $this->assertFalse($container->isClosure($serviceId));
        $this->assertFalse($container->isProvided($serviceId));
        $this->assertFalse($container->isPublished($serviceId));

        $this->assertInstanceOf($serviceId, $service = $container->get($serviceId));
        // A bound singleton should return the same instance each time it is retrieved
        $this->assertSame($service, $container->get($serviceId));

        $this->assertInstanceOf($serviceId, $container->getSingleton($serviceId));
        $this->assertSame($service, $container->getSingleton($serviceId));
    }

    public function testOffsetGetSetAndExists(): void
    {
        $container = $this->container;
        $serviceId = Service::class;

        $container[$serviceId] = $serviceId;

        $this->assertTrue(isset($container[$serviceId]));
        $this->assertInstanceOf($serviceId, $service = $container[$serviceId]);
        // A bound service should return a new instance each time it is gotten
        $this->assertNotSame($service, $container[$serviceId]);
    }

    public function testClosure(): void
    {
        $container = $this->container;
        $serviceId = self::class;
        $closure   = static function () {
            return new self();
        };

        $container->setClosure($serviceId, $closure);

        $this->assertTrue($container->has($serviceId));
        $this->assertTrue($container->isClosure($serviceId));
        // Set methods will automatically set the service id to published
        // Bounding is a deferment technique, whilst setting is not-deferred and hence should be used through providers
        $this->assertTrue($container->isPublished($serviceId));

        $this->assertFalse($container->isAlias($serviceId));
        $this->assertFalse($container->isSingleton($serviceId));
        $this->assertFalse($container->isService($serviceId));
        $this->assertFalse($container->isProvided($serviceId));

        $this->assertInstanceOf($serviceId, $service = $container->get($serviceId));
        // A bound service should return a new instance each time it is retrieved
        // Of course an application can choose to return the same instance each time, but why not use a singleton then?
        $this->assertNotSame($service, $container->get($serviceId));

        $this->assertInstanceOf($serviceId, $container->getClosure($serviceId));
        $this->assertNotSame($service, $container->getClosure($serviceId));
    }

    public function testOffsetUnset(): void
    {
        $container = $this->container;
        $serviceId = Service::class;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cannot remove service with name $serviceId from the container.");

        $container[$serviceId] = $serviceId;

        unset($container[$serviceId]);
    }

    public function testProvided(): void
    {
        $container = $this->container;

        $this->assertTrue($container->has(Dispatcher::class));
    }
}
