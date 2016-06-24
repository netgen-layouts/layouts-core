<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Registry;

use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use PHPUnit\Framework\TestCase;

class ConditionTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    protected $conditionType;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ConditionTypeRegistry();

        $this->conditionType = new ConditionType('type', 'value');
        $this->registry->addConditionType($this->conditionType);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::addConditionType
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionTypes
     */
    public function testGetConditionTypes()
    {
        self::assertEquals(array('type' => $this->conditionType), $this->registry->getConditionTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionType
     */
    public function testGetConditionType()
    {
        self::assertEquals($this->conditionType, $this->registry->getConditionType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionType
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetConditionTypeThrowsInvalidArgumentException()
    {
        $this->registry->getConditionType('other_type');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::hasConditionType
     */
    public function testHasConditionType()
    {
        self::assertTrue($this->registry->hasConditionType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::hasConditionType
     */
    public function testHasConditionTypeWithNoConditionType()
    {
        self::assertFalse($this->registry->hasConditionType('other_type'));
    }
}
