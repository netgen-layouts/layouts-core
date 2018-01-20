<?php

namespace Netgen\BlockManager\Tests\Exception\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterTypeException;
use PHPUnit\Framework\TestCase;

final class ParameterTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterTypeException::noParameterType
     */
    public function testNoParameterType()
    {
        $exception = ParameterTypeException::noParameterType('type');

        $this->assertEquals(
            'Parameter type with "type" identifier does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterTypeException::noParameterTypeClass
     */
    public function testNoParameterTypeClass()
    {
        $exception = ParameterTypeException::noParameterTypeClass('class');

        $this->assertEquals(
            'Parameter type with class "class" does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterTypeException::noFormMapper
     */
    public function testNoFormMapper()
    {
        $exception = ParameterTypeException::noFormMapper('type');

        $this->assertEquals(
            'Form mapper for "type" parameter type does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Parameters\ParameterTypeException::unsupportedParameterType
     */
    public function testUnsupportedParameterType()
    {
        $exception = ParameterTypeException::unsupportedParameterType('type');

        $this->assertEquals(
            'Parameter with "type" type is not supported.',
            $exception->getMessage()
        );
    }
}
