<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\BlockCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use PHPUnit\Framework\TestCase;

class BlockCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $blockCreateStruct = new BlockCreateStruct();

        $this->assertNull($blockCreateStruct->blockDefinition);
        $this->assertNull($blockCreateStruct->viewType);
        $this->assertNull($blockCreateStruct->itemViewType);
        $this->assertNull($blockCreateStruct->name);
    }

    public function testSetProperties()
    {
        $blockCreateStruct = new BlockCreateStruct(
            array(
                'blockDefinition' => new BlockDefinition(),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
            )
        );

        $this->assertEquals(new BlockDefinition(), $blockCreateStruct->blockDefinition);
        $this->assertEquals('default', $blockCreateStruct->viewType);
        $this->assertEquals('standard', $blockCreateStruct->itemViewType);
        $this->assertEquals('My block', $blockCreateStruct->name);
    }
}
