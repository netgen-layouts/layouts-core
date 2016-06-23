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

        self::assertTrue($parameter->isRequired());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::isRequired
     */
    public function testGetIsRequiredReturnsFalse()
    {
        $parameter = new Parameter();

        self::assertFalse($parameter->isRequired());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::getConstraints
     * @covers \Netgen\BlockManager\Parameters\Parameter::getBaseConstraints
     * @covers \Netgen\BlockManager\Parameters\Parameter::getParameterConstraints
     */
    public function testGetConstraints()
    {
        $parameter = new Parameter();

        self::assertEquals(array(), $parameter->getConstraints());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter::__construct
     * @covers \Netgen\BlockManager\Parameters\Parameter::getConstraints
     * @covers \Netgen\BlockManager\Parameters\Parameter::getBaseConstraints
     * @covers \Netgen\BlockManager\Parameters\Parameter::getParameterConstraints
     */
    public function testGetConstraintsForRequiredParameter()
    {
        $parameter = new Parameter(array(), true);

        self::assertEquals(
            array(new Constraints\NotBlank()),
            $parameter->getConstraints()
        );
    }
}
