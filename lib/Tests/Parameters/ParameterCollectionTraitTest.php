<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use PHPUnit\Framework\TestCase;

class ParameterCollectionTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameters
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::hasParameter
     */
    public function testDefaultProperties()
    {
        $parameterCollection = new ParameterCollection();

        $this->assertNull($parameterCollection->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameter
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameters
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::hasParameter
     */
    public function testSetProperties()
    {
        $parameterCollection = new ParameterCollection(
            array(
                'name' => 'value',
            )
        );

        $this->assertEquals(array('name' => 'value'), $parameterCollection->getParameters());

        $this->assertFalse($parameterCollection->hasParameter('test'));
        $this->assertTrue($parameterCollection->hasParameter('name'));

        try {
            $this->assertEquals(array(), $parameterCollection->getParameter('test'));
            $this->fail('Fetched a parameter in empty collection.');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }

        $this->assertEquals('value', $parameterCollection->getParameter('name'));
    }
}
