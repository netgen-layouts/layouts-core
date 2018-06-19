<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use PHPUnit\Framework\TestCase;

final class ItemCreateStructTest extends TestCase
{
    public function testSetProperties(): void
    {
        $itemDefinition = new ItemDefinition();

        $itemCreateStruct = new ItemCreateStruct(
            [
                'value' => 3,
                'definition' => $itemDefinition,
                'type' => Item::TYPE_OVERRIDE,
            ]
        );

        $this->assertSame($itemDefinition, $itemCreateStruct->definition);
        $this->assertSame(3, $itemCreateStruct->value);
        $this->assertSame(Item::TYPE_OVERRIDE, $itemCreateStruct->type);
    }
}
