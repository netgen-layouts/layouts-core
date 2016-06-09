<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use PHPUnit\Framework\TestCase;

class ItemCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $itemCreateStruct = new ItemCreateStruct();

        self::assertNull($itemCreateStruct->valueId);
        self::assertNull($itemCreateStruct->valueType);
        self::assertEquals(Item::TYPE_MANUAL, $itemCreateStruct->type);
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

        self::assertEquals(3, $itemCreateStruct->valueId);
        self::assertEquals('value_type', $itemCreateStruct->valueType);
        self::assertEquals(Item::TYPE_OVERRIDE, $itemCreateStruct->type);
    }
}
