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

namespace Valkyrja\Tests\Unit\Routing;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Routing\Models\Route;

use function get_class;

/**
 * Test the route model.
 *
 * @author Melech Mizrachi
 */
class RouteTest extends TestCase
{
    /**
     * The route model.
     */
    protected Route $route;

    /**
     * The string value to test with.
     */
    protected string $stringValue = 'test';

    /**
     * Get the route model to test with.
     */
    protected function getRoute(): Route
    {
        return $this->route ?? $this->route = new Route();
    }

    /**
     * Test the getPath getter method.
     */
    public function testPath(): void
    {
        $this->getRoute()->setPath($this->stringValue);

        self::assertEquals($this->stringValue, $this->getRoute()->getPath());
    }

    /**
     * Test the getRequestMethods getter method default value.
     */
    public function testGetRequestMethodsDefault(): void
    {
        self::assertEquals(
            [
                RequestMethod::GET,
                RequestMethod::HEAD,
            ],
            $this->getRoute()->getMethods()
        );
    }

    /**
     * Test the setRequestMethods setter method.
     */
    public function testSetRequestMethods(): void
    {
        $set = $this->getRoute()->setMethods([RequestMethod::POST]);

        self::assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the setRequestMethods setter method with invalid data.
     */
    public function testSetRequestMethodsInvalid(): void
    {
        try {
            $this->getRoute()->setMethods(['invalid value']);
        } catch (Exception $exception) {
            self::assertEquals(InvalidArgumentException::class, get_class($exception));
        }
    }

    /**
     * Test the getRequestMethods getter method.
     */
    public function testGetRequestMethods(): void
    {
        $value = [RequestMethod::POST];
        $this->getRoute()->setMethods($value);

        self::assertEquals($value, $this->getRoute()->getMethods());
    }

    /**
     * Test the getRegex getter method default value.
     */
    public function testGetRegexDefault(): void
    {
        self::assertEquals(null, $this->getRoute()->getRegex());
    }

    /**
     * Test the setRegex setter method.
     */
    public function testSetRegex(): void
    {
        $set = $this->getRoute()->setRegex($this->stringValue);

        self::assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the setRegex setter method using null.
     */
    public function testSetRegexNull(): void
    {
        $set = $this->getRoute()->setRegex(null);

        self::assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getRegex getter method.
     */
    public function testGetRegex(): void
    {
        $this->getRoute()->setRegex($this->stringValue);

        self::assertEquals($this->stringValue, $this->getRoute()->getRegex());
    }

    /**
     * Test the getMiddleware getter method default value.
     */
    public function testGetMiddlewareDefault(): void
    {
        self::assertEquals(null, $this->getRoute()->getMiddleware());
    }

    /**
     * Test the setMiddleware setter method.
     */
    public function testSetMiddleware(): void
    {
        $set = $this->getRoute()->setMiddleware([$this->stringValue]);

        self::assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the setMiddleware setter method using null.
     */
    public function testSetMiddlewareNull(): void
    {
        $set = $this->getRoute()->setMiddleware(null);

        self::assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getMiddleware getter method.
     */
    public function testGetMiddleware(): void
    {
        $this->getRoute()->setMiddleware([$this->stringValue]);

        self::assertEquals([$this->stringValue], $this->getRoute()->getMiddleware());
    }

    /**
     * Test the getDynamic getter method default value.
     */
    public function testGetDynamicDefault(): void
    {
        self::assertEquals(false, $this->getRoute()->isDynamic());
    }

    /**
     * Test the setDynamic setter method.
     */
    public function testSetDynamic(): void
    {
        $set = $this->getRoute()->setDynamic(true);

        self::assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getDynamic getter method.
     */
    public function testGetDynamic(): void
    {
        $this->getRoute()->setDynamic(true);

        self::assertEquals(true, $this->getRoute()->isDynamic());
    }

    /**
     * Test the getSecure getter method default value.
     */
    public function testGetSecureDefault(): void
    {
        self::assertEquals(false, $this->getRoute()->isSecure());
    }

    /**
     * Test the setSecure setter method.
     */
    public function testSetSecure(): void
    {
        $set = $this->getRoute()->setSecure(true);

        self::assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getSecure getter method.
     */
    public function testGetSecure(): void
    {
        $this->getRoute()->setSecure(true);

        self::assertEquals(true, $this->getRoute()->isSecure());
    }
}
