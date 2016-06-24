<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use PHPUnit\Framework\TestCase;

class BlockUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $blockUpdateStruct = new BlockUpdateStruct();

        self::assertNull($blockUpdateStruct->viewType);
        self::assertNull($blockUpdateStruct->itemViewType);
        self::assertNull($blockUpdateStruct->name);
    }

    public function testSetProperties()
    {
        $blockUpdateStruct = new BlockUpdateStruct(
            array(
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
            )
        );

        self::assertEquals('default', $blockUpdateStruct->viewType);
        self::assertEquals('standard', $blockUpdateStruct->itemViewType);
        self::assertEquals('My block', $blockUpdateStruct->name);
    }
}
