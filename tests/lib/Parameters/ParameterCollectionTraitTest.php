<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use PHPUnit\Framework\TestCase;

final class ParameterCollectionTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameterDefinition
     */
    public function testGetParameterDefinition()
    {
        $parameterDefinitions = ['name' => new ParameterDefinition()];
        $parameterCollection = new ParameterCollection($parameterDefinitions);

        $this->assertEquals(
            $parameterDefinitions['name'],
            $parameterCollection->getParameterDefinition('name')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameterDefinition
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterException
     * @expectedExceptionMessage Parameter definition with "test" name does not exist in the object.
     */
    public function testGetParameterDefinitionWithNonExistingDefinition()
    {
        $parameterDefinitions = ['name' => new ParameterDefinition()];
        $parameterCollection = new ParameterCollection($parameterDefinitions);

        $parameterCollection->getParameterDefinition('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameterDefinitions
     */
    public function testGetParameterDefinitions()
    {
        $parameterDefinitions = ['name' => new ParameterDefinition()];
        $parameterCollection = new ParameterCollection($parameterDefinitions);

        $this->assertEquals($parameterDefinitions, $parameterCollection->getParameterDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::hasParameterDefinition
     */
    public function testHasParameterDefinition()
    {
        $parameterDefinitions = ['name' => new ParameterDefinition()];
        $parameterCollection = new ParameterCollection($parameterDefinitions);

        $this->assertFalse($parameterCollection->hasParameterDefinition('test'));
        $this->assertTrue($parameterCollection->hasParameterDefinition('name'));
    }
}
