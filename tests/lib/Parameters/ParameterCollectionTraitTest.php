<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class ParameterCollectionTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::hasParameterDefinition
     */
    public function testDefaultProperties()
    {
        $parameterCollection = new ParameterCollection();

        $this->assertNull($parameterCollection->getParameterDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::hasParameterDefinition
     */
    public function testSetProperties()
    {
        $parameterDefinitions = array(
            'name' => new ParameterDefinition(
                array(
                    'name' => 'name',
                    'type' => new TextType(),
                )
            ),
        );

        $parameterCollection = new ParameterCollection($parameterDefinitions);

        $this->assertEquals($parameterDefinitions, $parameterCollection->getParameterDefinitions());

        $this->assertFalse($parameterCollection->hasParameterDefinition('test'));
        $this->assertTrue($parameterCollection->hasParameterDefinition('name'));

        try {
            $this->assertEquals(array(), $parameterCollection->getParameterDefinition('test'));
            $this->fail('Fetched a parameter in empty collection.');
        } catch (ParameterException $e) {
            // Do nothing
        }

        $this->assertEquals($parameterDefinitions['name'], $parameterCollection->getParameterDefinition('name'));
    }
}
