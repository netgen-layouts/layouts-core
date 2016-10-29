<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterDefinition\TextLine;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameterDefinition;
use PHPUnit\Framework\TestCase;
use stdClass;

class CompoundParameterDefinitionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::__construct
     * @expectedException \LogicException
     */
    public function testConstructorWithNonParameterObject()
    {
        new CompoundParameterDefinition(array('param' => new stdClass()), array(), true);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::__construct
     * @expectedException \LogicException
     */
    public function testConstructorWithCompoundParameterDefinitions()
    {
        new CompoundParameterDefinition(array('param' => new CompoundParameterDefinition()), array(), true);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::__construct
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::getParameters
     */
    public function testGetParameters()
    {
        $parameterDefinition = new CompoundParameterDefinition(array('param' => new TextLine()), array(), true);

        $this->assertEquals(array('param' => new TextLine()), $parameterDefinition->getParameters());
    }
}
