<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use PHPUnit\Framework\TestCase;

class ParameterTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::isRequired
     */
    public function testGetIsRequired()
    {
        $parameter = new Parameter(array(), true);

        $this->assertTrue($parameter->isRequired());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::isRequired
     */
    public function testGetIsRequiredReturnsFalse()
    {
        $parameter = new Parameter();

        $this->assertFalse($parameter->isRequired());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::getDefaultValue
     */
    public function testGetDefaultValue()
    {
        $parameter = new Parameter();

        $this->assertEquals(null, $parameter->getDefaultValue());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::getGroups
     */
    public function testGetDefaultGroups()
    {
        $parameter = new Parameter();

        $this->assertEquals(array(), $parameter->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::getGroups
     */
    public function testGetGroups()
    {
        $parameter = new Parameter(array(), false, null, array('group'));

        $this->assertEquals(array('group'), $parameter->getGroups());
    }
}
