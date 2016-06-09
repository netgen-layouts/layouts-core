<?php

namespace Netgen\BlockManager\Tests\Exception;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InvalidArgumentExceptionTest extends TestCase
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
