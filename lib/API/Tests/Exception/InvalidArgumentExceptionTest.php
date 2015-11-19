<?php

namespace Netgen\BlockManager\API\Tests\Exception;

use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class InvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Exception\InvalidArgumentException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new InvalidArgumentException('test', 'test_value', 'Value must be an integer.');

        self::assertEquals(
            'Argument test has an invalid value: test_value. Value must be an integer.',
            $exception->getMessage()
        );
    }
}
