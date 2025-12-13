<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(BlockList::class)]
final class BlockListTest extends TestCase
{
    public function testGetBlocks(): void
    {
        $blocks = [new Block(), new Block()];

        self::assertSame($blocks, BlockList::fromArray($blocks)->getBlocks());
    }

    public function testGetBlockIds(): void
    {
        $uuid1 = Uuid::v4();
        $uuid2 = Uuid::v4();

        $blocks = [Block::fromArray(['id' => $uuid1]), Block::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], BlockList::fromArray($blocks)->getBlockIds());
    }
}
