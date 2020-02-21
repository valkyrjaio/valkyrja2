<?php

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Tests\Unit\Http;

use PHPUnit\Framework\TestCase;
use Valkyrja\Http\Server;

/**
 * Test the server collection class.
 *
 * @author Melech Mizrachi
 */
class ServerTest extends TestCase
{
    /**
     * The class to test with.
     *
     * @var Server
     */
    protected Server $class;

    /**
     * The server array to test with.
     *
     * @var array
     */
    protected static array $server = [
        'NON_HEADER'     => 'test',
        'BOGUS'          => 'test',
        'CONTENT_TYPE'   => 'test',
        'CONTENT_LENGTH' => 'test',
        'HTTP_HEADER'    => 'test',
    ];

    /**
     * The headers that should be returned.
     *
     * @var array
     */
    protected static array $headers = [
        'Content-Type'   => 'test',
        'Content-Length' => 'test',
        'Header'         => 'test',
    ];

    /**
     * Setup the test.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->class = new Server(self::$server);
    }

    /**
     * Test the getHeaders method.
     *
     * @return void
     */
    public function testGetHeaders(): void
    {
        $this->assertEquals(self::$headers, $this->class->getHeaders());
    }
}
