<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use PHPUnit\Framework\TestCase;

final class ParameterDefinitionCollectionTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait::getParameterDefinition
     */
    public function testGetParameterDefinition()
    {
        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => new ParameterDefinition()]
        );

        $this->assertEquals(
            new ParameterDefinition(),
            $parameterDefinitions->getParameterDefinition('name')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait::getParameterDefinition
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterException
     * @expectedExceptionMessage Parameter definition with "test" name does not exist in the object.
     */
    public function testGetParameterDefinitionWithNonExistingDefinition()
    {
        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => new ParameterDefinition()]
        );

        $parameterDefinitions->getParameterDefinition('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait::getParameterDefinitions
     */
    public function testGetParameterDefinitions()
    {
        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => new ParameterDefinition()]
        );

        $this->assertEquals(
            ['name' => new ParameterDefinition()],
            $parameterDefinitions->getParameterDefinitions()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait::hasParameterDefinition
     */
    public function testHasParameterDefinition()
    {
        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => new ParameterDefinition()]
        );

        $this->assertFalse($parameterDefinitions->hasParameterDefinition('test'));
        $this->assertTrue($parameterDefinitions->hasParameterDefinition('name'));
    }
}
