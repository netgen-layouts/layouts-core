<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Registry;

use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use PHPUnit\Framework\TestCase;

class TargetTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new TargetTypeRegistry();

        $this->targetType = new TargetType('type', 'value');
        $this->registry->addTargetType($this->targetType);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::addTargetType
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getTargetTypes
     */
    public function testGetTargetTypes()
    {
        $this->assertEquals(array('type' => $this->targetType), $this->registry->getTargetTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getTargetType
     */
    public function testGetTargetType()
    {
        $this->assertEquals($this->targetType, $this->registry->getTargetType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::getTargetType
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetTargetTypeThrowsInvalidArgumentException()
    {
        $this->registry->getTargetType('other_type');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::hasTargetType
     */
    public function testHasTargetType()
    {
        $this->assertTrue($this->registry->hasTargetType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry::hasTargetType
     */
    public function testHasTargetTypeWithNoTargetType()
    {
        $this->assertFalse($this->registry->hasTargetType('other_type'));
    }
}
