<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use PHPUnit\Framework\TestCase;

final class CompoundParameterDefinitionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::__construct
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::getParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::hasParameterDefinition
     */
    public function testDefaultProperties()
    {
        $parameterDefinition = new CompoundParameterDefinition();

        $this->assertEquals(array(), $parameterDefinition->getParameterDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::getParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::hasParameterDefinition
     */
    public function testSetProperties()
    {
        $parameterDefinition = new CompoundParameterDefinition(
            array(
                'parameterDefinitions' => array('name' => 'value'),
            )
        );

        $this->assertEquals(array('name' => 'value'), $parameterDefinition->getParameterDefinitions());

        $this->assertFalse($parameterDefinition->hasParameterDefinition('test'));
        $this->assertTrue($parameterDefinition->hasParameterDefinition('name'));

        try {
            $this->assertEquals(array(), $parameterDefinition->getParameterDefinition('test'));
            $this->fail('Fetched a parameter in empty collection.');
        } catch (ParameterException $e) {
            // Do nothing
        }

        $this->assertEquals('value', $parameterDefinition->getParameterDefinition('name'));
    }
}
