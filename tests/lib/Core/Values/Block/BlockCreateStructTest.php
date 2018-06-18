<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use PHPUnit\Framework\TestCase;

final class BlockCreateStructTest extends TestCase
{
    public function testSetProperties(): void
    {
        $blockDefinition = new BlockDefinition();

        $blockCreateStruct = new BlockCreateStruct(
            [
                'definition' => $blockDefinition,
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'alwaysAvailable' => false,
            ]
        );

        $this->assertSame($blockDefinition, $blockCreateStruct->definition);
        $this->assertSame('default', $blockCreateStruct->viewType);
        $this->assertSame('standard', $blockCreateStruct->itemViewType);
        $this->assertSame('My block', $blockCreateStruct->name);
        $this->assertTrue($blockCreateStruct->isTranslatable);
        $this->assertFalse($blockCreateStruct->alwaysAvailable);
    }
}
