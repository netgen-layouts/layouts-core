<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

final class BlockListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Block\BlockList::__construct
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

        self::assertSame(
            [$uuid1->toString(), $uuid2->toString()],
            array_map('strval', (new BlockList($blocks))->getBlockIds())
        );
    }
}
