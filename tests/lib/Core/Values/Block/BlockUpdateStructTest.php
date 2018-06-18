<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use PHPUnit\Framework\TestCase;

final class BlockUpdateStructTest extends TestCase
{
    public function testSetProperties(): void
    {
        $blockUpdateStruct = new BlockUpdateStruct(
            [
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'alwaysAvailable' => false,
                'locale' => 'en',
            ]
        );

        $this->assertSame('default', $blockUpdateStruct->viewType);
        $this->assertSame('standard', $blockUpdateStruct->itemViewType);
        $this->assertSame('My block', $blockUpdateStruct->name);
        $this->assertFalse($blockUpdateStruct->alwaysAvailable);
        $this->assertSame('en', $blockUpdateStruct->locale);
    }
}
