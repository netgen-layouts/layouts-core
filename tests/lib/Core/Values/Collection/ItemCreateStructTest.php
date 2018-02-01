<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use PHPUnit\Framework\TestCase;

final class ItemCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $itemCreateStruct = new ItemCreateStruct();

        $this->assertNull($itemCreateStruct->value);
        $this->assertNull($itemCreateStruct->valueType);
        $this->assertEquals(Item::TYPE_MANUAL, $itemCreateStruct->type);
    }

    public function testSetProperties()
    {
        $itemCreateStruct = new ItemCreateStruct(
            array(
                'value' => 3,
                'valueType' => 'value_type',
                'type' => Item::TYPE_OVERRIDE,
            )
        );

        $this->assertEquals(3, $itemCreateStruct->value);
        $this->assertEquals('value_type', $itemCreateStruct->valueType);
        $this->assertEquals(Item::TYPE_OVERRIDE, $itemCreateStruct->type);
    }
}
