<?php

namespace Netgen\BlockManager\Tests\Exception\Validation;

use Netgen\BlockManager\Exception\Validation\ValidationFailedException;
use PHPUnit\Framework\TestCase;

class ValidationFailedExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Validation\ValidationFailedException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new ValidationFailedException('param', 'Some error');

        $this->assertEquals(
            'There was an error validating "param": Some error',
            $exception->getMessage()
        );
    }
}
