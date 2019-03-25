<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class BlockListTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                BlockList::class,
                str_replace('\BlockList', '', BlockList::class),
                Block::class,
                stdClass::class
            )
        );

        new BlockList([new Block(), new stdClass(), new Block()]);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockList::__construct
     * @covers \Netgen\BlockManager\API\Values\Block\BlockList::getBlocks
     */
    public function testGetBlocks(): void
    {
        $blocks = [new Block(), new Block()];

        self::assertSame($blocks, (new BlockList($blocks))->getBlocks());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockList::getBlockIds
     */
    public function testGetBlockIds(): void
    {
        $blocks = [Block::fromArray(['id' => 42]), Block::fromArray(['id' => 24])];

        self::assertSame([42, 24], (new BlockList($blocks))->getBlockIds());
    }
}
