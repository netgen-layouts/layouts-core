<?php

namespace Netgen\BlockManager\Tests\Exception;

use Netgen\BlockManager\Exception\NotFoundException;

class NotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\NotFoundException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new NotFoundException('test', 1);

        self::assertEquals(
            'Could not find test with identifier "1"',
            $exception->getMessage()
        );
    }
}
