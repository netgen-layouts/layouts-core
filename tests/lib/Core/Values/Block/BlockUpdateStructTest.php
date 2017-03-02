<?php

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use PHPUnit\Framework\TestCase;

class BlockUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $blockUpdateStruct = new BlockUpdateStruct();

        $this->assertNull($blockUpdateStruct->viewType);
        $this->assertNull($blockUpdateStruct->itemViewType);
        $this->assertNull($blockUpdateStruct->name);
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

        $this->assertEquals('default', $blockUpdateStruct->viewType);
        $this->assertEquals('standard', $blockUpdateStruct->itemViewType);
        $this->assertEquals('My block', $blockUpdateStruct->name);
    }
}
