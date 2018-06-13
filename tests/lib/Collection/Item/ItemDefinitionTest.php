<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Config\ConfigDefinition;
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
            [
                'valueType' => 'value_type',
                'configDefinitions' => ['config' => new ConfigDefinition()],
            ]
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
            ['config' => new ConfigDefinition()],
            $this->itemDefinition->getConfigDefinitions()
        );
    }
}
