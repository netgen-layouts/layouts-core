<?php

namespace Netgen\BlockManager\Tests\Exception\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use PHPUnit\Framework\TestCase;

final class ParameterExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterException::noParameterDefinition
     */
    public function testNoParameterDefinition()
    {
        $exception = ParameterException::noParameterDefinition('param');

        $this->assertEquals(
            'Parameter definition with "param" name does not exist in the object.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterException::noOption
     */
    public function testNoOption()
    {
        $exception = ParameterException::noOption('opt');

        $this->assertEquals(
            'Option "opt" does not exist in the parameter definition.',
            $exception->getMessage()
        );
    }
}
