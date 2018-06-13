<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use PHPUnit\Framework\TestCase;

final class ItemCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $itemCreateStruct = new ItemCreateStruct();

        $this->assertEquals(Item::TYPE_MANUAL, $itemCreateStruct->type);
    }

    public function testSetProperties()
    {
        $itemCreateStruct = new ItemCreateStruct(
            [
                'value' => 3,
                'definition' => new ItemDefinition(),
                'type' => Item::TYPE_OVERRIDE,
            ]
        );

        $this->assertEquals(new ItemDefinition(), $itemCreateStruct->definition);
        $this->assertEquals(3, $itemCreateStruct->value);
        $this->assertEquals(Item::TYPE_OVERRIDE, $itemCreateStruct->type);
    }
}
