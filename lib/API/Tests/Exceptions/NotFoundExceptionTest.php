<?php

namespace Netgen\BlockManager\API\Tests\Exceptions;

use Netgen\BlockManager\API\Exceptions\NotFoundException;
use PHPUnit_Framework_TestCase;

class NotFoundExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Exceptions\NotFoundException::__construct
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
