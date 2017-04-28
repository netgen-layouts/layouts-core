<?php

namespace Netgen\BlockManager\Tests\Exception\Validation;

use Netgen\BlockManager\Exception\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

class ValidationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Validation\ValidationException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = ValidationException::validationFailed('param', 'Some error');

        $this->assertEquals(
            'There was an error validating "param": Some error',
            $exception->getMessage()
        );
    }
}
