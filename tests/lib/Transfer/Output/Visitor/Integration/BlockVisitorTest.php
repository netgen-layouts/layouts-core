<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Transfer\Output\Visitor\BlockVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class BlockVisitorTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->getVisitor()->visit(new Block());
    }

    public function getVisitor(): VisitorInterface
    {
        return new BlockVisitor($this->blockService);
    }

    public function acceptProvider(): array
    {
        return [
            [new Block(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): APIBlock { return $this->blockService->loadBlock(31); }, 'block/block_31.json'],
            [function (): APIBlock { return $this->blockService->loadBlock(33); }, 'block/block_33.json'],
        ];
    }
}
