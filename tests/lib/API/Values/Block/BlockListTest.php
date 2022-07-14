<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

use function sprintf;
use function str_replace;

final class BlockListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Block\BlockList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/(must be an instance of|must be of type) %s, (instance of )?%s given/',
                str_replace('\\', '\\\\', Block::class),
                stdClass::class,
            ),
        );

        new BlockList([new Block(), new stdClass(), new Block()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Block\BlockList::__construct
     * @covers \Netgen\Layouts\API\Values\Block\BlockList::getBlocks
     */
    public function testGetBlocks(): void
    {
        $blocks = [new Block(), new Block()];

        self::assertSame($blocks, (new BlockList($blocks))->getBlocks());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Block\BlockList::getBlockIds
     */
    public function testGetBlockIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $blocks = [Block::fromArray(['id' => $uuid1]), Block::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], (new BlockList($blocks))->getBlockIds());
    }
}
