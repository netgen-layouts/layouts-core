<?php

namespace Netgen\BlockManager\API\Tests\Exceptions;

use Netgen\BlockManager\API\Exceptions\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class InvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Exceptions\InvalidArgumentException::__construct
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
