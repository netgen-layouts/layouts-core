<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Output\Visitor\BlockVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

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
