<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use PHPUnit\Framework\TestCase;

class BlockCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $blockCreateStruct = new BlockCreateStruct();

        $this->assertNull($blockCreateStruct->definitionIdentifier);
        $this->assertNull($blockCreateStruct->viewType);
        $this->assertNull($blockCreateStruct->itemViewType);
        $this->assertNull($blockCreateStruct->name);
    }

    public function testSetProperties()
    {
        $blockCreateStruct = new BlockCreateStruct(
            array(
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
            )
        );

        $this->assertEquals('text', $blockCreateStruct->definitionIdentifier);
        $this->assertEquals('default', $blockCreateStruct->viewType);
        $this->assertEquals('standard', $blockCreateStruct->itemViewType);
        $this->assertEquals('My block', $blockCreateStruct->name);
    }
}
