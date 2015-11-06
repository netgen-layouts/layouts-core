<?php

namespace Netgen\BlockManager\Exceptions\Tests;

use Netgen\BlockManager\Exceptions\NotFoundException;
use PHPUnit_Framework_TestCase;

class NotFoundExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exceptions\NotFoundException::__construct
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
