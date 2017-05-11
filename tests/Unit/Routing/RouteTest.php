<?php

namespace Valkyrja\Tests\Unit\Routing;

use PHPUnit\Framework\TestCase;
use Valkyrja\Http\RequestMethod;
use Valkyrja\Routing\Route;

/**
 * Test the route model.
 *
 * @author Melech Mizrachi
 */
class RouteTest extends TestCase
{
    /**
     * The route model.
     *
     * @var \Valkyrja\Routing\Route
     */
    protected $route;

    /**
     * The string value to test with.
     *
     * @var string
     */
    protected $stringValue = 'test';

    /**
     * Get the route model to test with.
     *
     * @return \Valkyrja\Routing\Route
     */
    protected function getRoute(): Route
    {
        return $this->route ?? $this->route = new Route();
    }

    /**
     * Test the getPath getter method default value.
     *
     * @return void
     */
    public function testGetPathDefault(): void
    {
        $this->assertEquals(null, $this->getRoute()->getPath());
    }

    /**
     * Test the setPath setter method.
     *
     * @return void
     */
    public function testSetPath(): void
    {
        $set = $this->getRoute()->setPath($this->stringValue);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getPath getter method.
     *
     * @return void
     */
    public function testGetPath(): void
    {
        $this->getRoute()->setPath($this->stringValue);

        $this->assertEquals($this->stringValue, $this->getRoute()->getPath());
    }

    /**
     * Test the getRequestMethods getter method default value.
     *
     * @return void
     */
    public function testGetRequestMethodsDefault(): void
    {
        $this->assertEquals(
            [
                RequestMethod::GET,
                RequestMethod::HEAD,
            ],
            $this->getRoute()->getRequestMethods()
        );
    }

    /**
     * Test the setRequestMethods setter method.
     *
     * @return void
     */
    public function testSetRequestMethods(): void
    {
        $set = $this->getRoute()->setRequestMethods([RequestMethod::POST]);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getRequestMethods getter method.
     *
     * @return void
     */
    public function testGetRequestMethods(): void
    {
        $value = [RequestMethod::POST];
        $this->getRoute()->setRequestMethods($value);

        $this->assertEquals($value, $this->getRoute()->getRequestMethods());
    }

    /**
     * Test the getRegex getter method default value.
     *
     * @return void
     */
    public function testGetRegexDefault(): void
    {
        $this->assertEquals(null, $this->getRoute()->getRegex());
    }

    /**
     * Test the setRegex setter method.
     *
     * @return void
     */
    public function testSetRegex(): void
    {
        $set = $this->getRoute()->setRegex($this->stringValue);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the setRegex setter method using null.
     *
     * @return void
     */
    public function testSetRegexNull(): void
    {
        $set = $this->getRoute()->setRegex(null);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getRegex getter method.
     *
     * @return void
     */
    public function testGetRegex(): void
    {
        $this->getRoute()->setRegex($this->stringValue);

        $this->assertEquals($this->stringValue, $this->getRoute()->getRegex());
    }

    /**
     * Test the getParams getter method default value.
     *
     * @return void
     */
    public function testGetParamsDefault(): void
    {
        $this->assertEquals(null, $this->getRoute()->getParams());
    }

    /**
     * Test the setParams setter method.
     *
     * @return void
     */
    public function testSetParams(): void
    {
        $set = $this->getRoute()->setParams([$this->stringValue]);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the setParams setter method using null.
     *
     * @return void
     */
    public function testSetParamsNull(): void
    {
        $set = $this->getRoute()->setParams(null);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getParams getter method.
     *
     * @return void
     */
    public function testGetParams(): void
    {
        $this->getRoute()->setParams([$this->stringValue]);

        $this->assertEquals([$this->stringValue], $this->getRoute()->getParams());
    }

    /**
     * Test the getSegments getter method default value.
     *
     * @return void
     */
    public function testGetSegmentsDefault(): void
    {
        $this->assertEquals(null, $this->getRoute()->getSegments());
    }

    /**
     * Test the setSegments setter method.
     *
     * @return void
     */
    public function testSetSegments(): void
    {
        $set = $this->getRoute()->setSegments([$this->stringValue]);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the setSegments setter method using null.
     *
     * @return void
     */
    public function testSetSegmentsNull(): void
    {
        $set = $this->getRoute()->setSegments(null);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getSegments getter method.
     *
     * @return void
     */
    public function testGetSegments(): void
    {
        $this->getRoute()->setSegments([$this->stringValue]);

        $this->assertEquals([$this->stringValue], $this->getRoute()->getSegments());
    }

    /**
     * Test the getDynamic getter method default value.
     *
     * @return void
     */
    public function testGetDynamicDefault(): void
    {
        $this->assertEquals(false, $this->getRoute()->getDynamic());
    }

    /**
     * Test the setDynamic setter method.
     *
     * @return void
     */
    public function testSetDynamic(): void
    {
        $set = $this->getRoute()->setDynamic(true);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getDynamic getter method.
     *
     * @return void
     */
    public function testGetDynamic(): void
    {
        $this->getRoute()->setDynamic(true);

        $this->assertEquals(true, $this->getRoute()->getDynamic());
    }

    /**
     * Test the getSecure getter method default value.
     *
     * @return void
     */
    public function testGetSecureDefault(): void
    {
        $this->assertEquals(false, $this->getRoute()->getSecure());
    }

    /**
     * Test the setSecure setter method.
     *
     * @return void
     */
    public function testSetSecure(): void
    {
        $set = $this->getRoute()->setSecure(true);

        $this->assertEquals(true, $set instanceof Route);
    }

    /**
     * Test the getSecure getter method.
     *
     * @return void
     */
    public function testGetSecure(): void
    {
        $this->getRoute()->setSecure(true);

        $this->assertEquals(true, $this->getRoute()->getSecure());
    }

    /**
     * Test the getRoute method.
     *
     * @return void
     */
    public function testGetRoute(): void
    {
        $route = Route::getRoute(['path' => $this->stringValue]);

        $this->assertEquals(true, $route instanceof Route);
    }

    /**
     * Test the __set_state magic method.
     *
     * @covers \Valkyrja\Routing\Route::__set_state()
     *
     * @return void
     */
    public function testSetState(): void
    {
        /* @noinspection ImplicitMagicMethodCallInspection */
        $route = Route::__set_state(['path' => $this->stringValue]);

        $this->assertEquals(true, $route instanceof Route);
    }
}
