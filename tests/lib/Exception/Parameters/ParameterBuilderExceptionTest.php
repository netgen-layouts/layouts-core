<?php

namespace Netgen\BlockManager\Tests\Exception\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterBuilderException;
use PHPUnit\Framework\TestCase;

final class ParameterBuilderExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException::noParameter
     */
    public function testNoParameter()
    {
        $exception = ParameterBuilderException::noParameter('param');

        $this->assertEquals(
            'Parameter with "param" name does not exist in the builder.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException::noOption
     */
    public function testNoOption()
    {
        $exception = ParameterBuilderException::noOption('opt', 'param');

        $this->assertEquals(
            'Option "opt" does not exist in the builder for "param" parameter.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException::subCompound
     */
    public function testSubCompound()
    {
        $exception = ParameterBuilderException::subCompound();

        $this->assertEquals(
            'Compound parameters cannot be added to compound parameters.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException::nonCompound
     */
    public function testNonCompound()
    {
        $exception = ParameterBuilderException::nonCompound();

        $this->assertEquals(
            'Parameters cannot be added to non-compound parameters.',
            $exception->getMessage()
        );
    }
}
