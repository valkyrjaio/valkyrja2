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

namespace Valkyrja\Tests\Unit\Annotations;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Valkyrja\Annotation\Annotation;
use Valkyrja\Annotation\Config\Config;
use Valkyrja\Annotation\Constants\AnnotationName;
use Valkyrja\Annotation\Constants\ConfigValue;
use Valkyrja\Annotation\Constants\Regex;
use Valkyrja\Annotation\Parsers\Parser;
use Valkyrja\Console\Annotations\Command;
use Valkyrja\Container\Annotations\Service;
use Valkyrja\Container\Annotations\Service\Context;
use Valkyrja\Event\Annotations\Listener;
use Valkyrja\Routing\Annotations\Route;

/**
 * Test the AnnotationsParser class.
 *
 * @description A description to test with
 *
 * @author      Melech Mizrachi
 *
 * @param string $param A description test
 *
 * @Route("path" = "/", "name" = "noAClass::Property")
 */
class AnnotationsParserTest extends TestCase
{
    /**
     * A static property to test with.
     */
    public static string $property = 'test';

    /**
     * A static property to test with for invalid key array (Line 257).
     *
     * @var string
     */
    public static $invalidKeyArray = ['test'];

    /**
     * The class to test with.
     */
    protected Parser $class;

    /**
     * The value to test with.
     */
    protected string $value = 'test';

    /**
     * Setup the test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->class = new Parser((array) new Config(ConfigValue::$defaults, true));
    }

    /**
     * A static method to test with.
     */
    public static function staticMethod(): string
    {
        return 'staticMethod';
    }

    /**
     * Test the getAnnotations method.
     *
     * @throws ReflectionException
     */
    public function testGetAnnotations(): void
    {
        $docString = (new ReflectionClass(self::class))->getDocComment();

        self::assertCount(4, $this->class->getAnnotations($docString));
    }

    /**
     * Test the getArguments method.
     */
    public function testGetArguments(): void
    {
        $arguments = '"path" = "", '
            . '"name" = "test", '
            // Test for line 413
            . '"empty" = "", '
            . '"requestMethods" = ["POST", "GET", "HEAD"], '
            . '"constant" = "Valkyrja\\\\Application::VERSION", '
            . '"property" = "Valkyrja\\\\Tests\\\\Unit\\\\Annotations\\\\AnnotationsParserTest::property", '
            . '"method" = "Valkyrja\\\\Tests\\\\Unit\\\\Annotations\\\\AnnotationsParserTest::staticMethod"';

        self::assertCount(7, $this->class->getPropertiesAsArray($arguments));
    }

    /**
     * Test the getArguments method.
     */
    public function testGetArgumentsNull(): void
    {
        self::assertEquals(null, $this->class->getPropertiesAsArray(null));
    }

    /**
     * Test the getRegex method.
     */
    public function testGetRegex(): void
    {
        self::assertEquals(Regex::REGEX, $this->class->getRegex());
    }

    /**
     * Test the getAnnotationsMap method.
     */
    public function testGetAnnotationsMap(): void
    {
        self::assertEquals(ConfigValue::MAP, $this->class->getAnnotationsMap());
    }

    /**
     * Test the getAnnotationFromMap method.
     */
    public function testGetAnnotationFromMap(): void
    {
        self::assertEquals(true, $this->class->getAnnotationFromMap('Bogus') instanceof Annotation);
    }

    /**
     * Test the getAnnotationFromMap method with a Command.
     */
    public function testGetCommandAnnotationFromMap(): void
    {
        self::assertEquals(true, $this->class->getAnnotationFromMap(AnnotationName::COMMAND) instanceof Command);
    }

    /**
     * Test the getAnnotationFromMap method with a Listener.
     */
    public function testGetListenerAnnotationFromMap(): void
    {
        self::assertEquals(true, $this->class->getAnnotationFromMap(AnnotationName::LISTENER) instanceof Listener);
    }

    /**
     * Test the getAnnotationFromMap method with a Route.
     */
    public function testGetRouteAnnotationFromMap(): void
    {
        self::assertEquals(true, $this->class->getAnnotationFromMap(AnnotationName::ROUTE) instanceof Route);
    }

    /**
     * Test the getAnnotationFromMap method with a Service.
     */
    public function testGetServiceAnnotationFromMap(): void
    {
        self::assertEquals(true, $this->class->getAnnotationFromMap(AnnotationName::SERVICE) instanceof Service);
    }

    /**
     * Test the getAnnotationFromMap method with a ServiceAlias.
     */
    public function testGetServiceAliasAnnotationFromMap(): void
    {
        self::assertEquals(
            true,
            $this->class->getAnnotationFromMap(AnnotationName::SERVICE_ALIAS) instanceof Service\Alias
        );
    }

    /**
     * Test the getAnnotationFromMap method with a ServiceContext.
     */
    public function testGetServiceContextAnnotationFromMap(): void
    {
        self::assertEquals(
            true,
            $this->class->getAnnotationFromMap(AnnotationName::SERVICE_CONTEXT) instanceof Context
        );
    }
}
