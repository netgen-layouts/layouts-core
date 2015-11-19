<?php

namespace Netgen\BlockManager\API\Tests\Exception;

use Netgen\BlockManager\API\Exception\NotFoundException;
use PHPUnit_Framework_TestCase;

class NotFoundExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Exception\NotFoundException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new NotFoundException('test', 1);

        self::assertEquals(
            'Could not find test with identifier 1',
            $exception->getMessage()
        );
    }
}
