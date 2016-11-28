<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use PHPUnit\Framework\TestCase;

class ItemCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $itemCreateStruct = new ItemCreateStruct();

        $this->assertNull($itemCreateStruct->valueId);
        $this->assertNull($itemCreateStruct->valueType);
        $this->assertEquals(Item::TYPE_MANUAL, $itemCreateStruct->type);
    }

    public function testSetProperties()
    {
        $itemCreateStruct = new ItemCreateStruct(
            array(
                'valueId' => 3,
                'valueType' => 'value_type',
                'type' => Item::TYPE_OVERRIDE,
            )
        );

        $this->assertEquals(3, $itemCreateStruct->valueId);
        $this->assertEquals('value_type', $itemCreateStruct->valueType);
        $this->assertEquals(Item::TYPE_OVERRIDE, $itemCreateStruct->type);
    }
}
