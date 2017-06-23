<?php

namespace Netgen\BlockManager\Tests\Exception\Core;

use Netgen\BlockManager\Exception\Core\ParameterException;
use PHPUnit\Framework\TestCase;

class ParameterExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Core\ParameterException::noParameter
     */
    public function testNoParameter()
    {
        $exception = ParameterException::noParameter('param');

        $this->assertEquals(
            'Parameter with "param" name does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Core\ParameterException::noParameterValue
     */
    public function testNoParameterValue()
    {
        $exception = ParameterException::noParameterValue('param');

        $this->assertEquals(
            'Parameter value for "param" parameter does not exist.',
            $exception->getMessage()
        );
    }
}
