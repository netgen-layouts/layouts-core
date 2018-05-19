<?php

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use PHPUnit\Framework\TestCase;

final class BlockCreateStructTest extends TestCase
{
    public function testSetProperties()
    {
        $blockCreateStruct = new BlockCreateStruct(
            [
                'definition' => new BlockDefinition(),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'alwaysAvailable' => false,
            ]
        );

        $this->assertEquals(new BlockDefinition(), $blockCreateStruct->definition);
        $this->assertEquals('default', $blockCreateStruct->viewType);
        $this->assertEquals('standard', $blockCreateStruct->itemViewType);
        $this->assertEquals('My block', $blockCreateStruct->name);
        $this->assertTrue($blockCreateStruct->isTranslatable);
        $this->assertFalse($blockCreateStruct->alwaysAvailable);
    }
}
