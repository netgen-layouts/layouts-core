<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use PHPUnit\Framework\TestCase;

final class BlockCreateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::__construct
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::getDefinition
     */
    public function testConstructor(): void
    {
        $blockDefinition = new BlockDefinition();

        $blockCreateStruct = new BlockCreateStruct($blockDefinition);

        $this->assertSame($blockDefinition, $blockCreateStruct->getDefinition());
    }
}
