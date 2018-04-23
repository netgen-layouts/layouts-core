<?php

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\Collection\Item\NullItemDefinition;
use PHPUnit\Framework\TestCase;

final class NullItemDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Item\NullItemDefinition
     */
    private $itemDefinition;

    public function setUp()
    {
        $this->itemDefinition = new NullItemDefinition();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\NullItemDefinition::getValueType
     */
    public function testGetValueType()
    {
        $this->assertEquals('null', $this->itemDefinition->getValueType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\NullItemDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions()
    {
        $this->assertEquals([], $this->itemDefinition->getConfigDefinitions());
    }
}
