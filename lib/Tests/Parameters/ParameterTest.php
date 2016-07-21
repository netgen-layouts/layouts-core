<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Symfony\Component\Validator\Constraints;
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
     * @covers \Netgen\BlockManager\Parameters\Parameter::getConstraints
     * @covers \Netgen\BlockManager\Parameters\Parameter::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\Parameter::getValueConstraints
     */
    public function testGetConstraints()
    {
        $parameter = new Parameter();

        $this->assertEquals(array(), $parameter->getConstraints());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::getConstraints
     * @covers \Netgen\BlockManager\Parameters\Parameter::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\Parameter::getValueConstraints
     */
    public function testGetConstraintsForRequiredParameter()
    {
        $parameter = new Parameter(array(), true);

        $this->assertEquals(
            array(new Constraints\NotBlank()),
            $parameter->getConstraints()
        );
    }
}
