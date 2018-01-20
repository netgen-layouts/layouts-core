<?php

namespace Netgen\BlockManager\Tests\Exception;

use Netgen\BlockManager\Exception\InvalidInterfaceException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class InvalidInterfaceExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\InvalidInterfaceException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new InvalidInterfaceException('Type', 'test', stdClass::class);

        $this->assertEquals(
            'Type "test" needs to implement "stdClass" interface.',
            $exception->getMessage()
        );
    }
}
