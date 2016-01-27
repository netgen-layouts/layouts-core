<?php

namespace Netgen\BlockManager\Tests\API\Exception;

use Netgen\BlockManager\API\Exception\BadStateException;

class BadStateExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Exception\BadStateException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new BadStateException('test', 'Value must be an integer.');

        self::assertEquals(
            'Argument "test" has an invalid state. Value must be an integer.',
            $exception->getMessage()
        );
    }
}
