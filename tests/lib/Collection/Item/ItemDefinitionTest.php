<?php

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Tests\Config\Stubs\CollectionItem\VisibilityConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use PHPUnit\Framework\TestCase;

final class ItemDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinition
     */
    private $itemDefinition;

    public function setUp()
    {
        $this->itemDefinition = new ItemDefinition(
            array(
                'valueType' => 'value_type',
                'configDefinitions' => array('visibility' => new ConfigDefinition('visibility', new VisibilityConfigHandler())),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\ItemDefinition::getValueType
     */
    public function testGetValueType()
    {
        $this->assertEquals('value_type', $this->itemDefinition->getValueType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\ItemDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions()
    {
        $this->assertEquals(
            array('visibility' => new ConfigDefinition('visibility', new VisibilityConfigHandler())),
            $this->itemDefinition->getConfigDefinitions()
        );
    }
}
