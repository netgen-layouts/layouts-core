<?php

namespace Netgen\BlockManager\Tests\API\Exception;

use Netgen\BlockManager\API\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Exception\InvalidArgumentException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new InvalidArgumentException('test', 'test_value', 'Value must be an integer.');

        self::assertEquals(
            'Argument "test" has an invalid value: "test_value". Value must be an integer.',
            $exception->getMessage()
        );
    }
}
