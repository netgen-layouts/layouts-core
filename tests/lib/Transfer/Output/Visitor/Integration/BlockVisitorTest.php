<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor\BlockVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class BlockVisitorTest extends VisitorTest
{
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Implementation requires sub-visitor');

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
            [function (): Block { return $this->blockService->loadBlock(31); }, 'block/block_31.json'],
            [function (): Block { return $this->blockService->loadBlock(33); }, 'block/block_33.json'],
        ];
    }
}
