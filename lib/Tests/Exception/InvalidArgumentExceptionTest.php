<?php

namespace Netgen\BlockManager\Tests\Exception;

use Netgen\BlockManager\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\InvalidArgumentException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new InvalidArgumentException('test', 'Value must be an integer.');

        self::assertEquals(
            'Argument "test" has an invalid value. Value must be an integer.',
            $exception->getMessage()
        );
    }
}
