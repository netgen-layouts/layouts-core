<?php

namespace Netgen\BlockManager\Tests\Parameters\Registry;

use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterType;
use PHPUnit\Framework\TestCase;

class ParameterTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType
     */
    protected $parameterType;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ParameterTypeRegistry();

        $this->parameterType = new ParameterType();

        $this->registry->addParameterType($this->parameterType);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::addParameterType
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterTypes
     */
    public function testAddParameterType()
    {
        $this->assertEquals(array('type' => $this->parameterType), $this->registry->getParameterTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::hasParameterType
     */
    public function testHasParameterType()
    {
        $this->assertTrue($this->registry->hasParameterType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::hasParameterType
     */
    public function testHasParameterTypeWithNoParameterType()
    {
        $this->assertFalse($this->registry->hasParameterType('other_type'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterType
     */
    public function testGetParameterType()
    {
        $this->assertEquals($this->parameterType, $this->registry->getParameterType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterType
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Parameter type with "other_type" identifier does not exist.
     */
    public function testGetParameterTypeThrowsInvalidArgumentException()
    {
        $this->registry->getParameterType('other_type');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterTypeByClass
     */
    public function testGetParameterTypeByClass()
    {
        $this->assertEquals($this->parameterType, $this->registry->getParameterTypeByClass(ParameterType::class));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry::getParameterTypeByClass
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Parameter type with class "SomeClass" does not exist.
     */
    public function testGetParameterTypeByClassThrowsInvalidArgumentException()
    {
        $this->registry->getParameterTypeByClass('SomeClass');
    }
}
