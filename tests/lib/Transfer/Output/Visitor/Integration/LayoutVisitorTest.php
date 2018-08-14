<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Transfer\Output\Visitor\LayoutVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class LayoutVisitorTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->layoutService = $this->createLayoutService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->getVisitor()->visit(new Layout());
    }

    public function getVisitor(): VisitorInterface
    {
        return new LayoutVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Layout(), true],
            [new Zone(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): Layout { return $this->layoutService->loadLayout(1); }, 'layout/layout_1.json'],
            [function (): Layout { return $this->layoutService->loadLayout(2); }, 'layout/layout_2.json'],
            [function (): Layout { return $this->layoutService->loadLayout(5); }, 'layout/layout_5.json'],
            [function (): Layout { return $this->layoutService->loadLayoutDraft(7); }, 'layout/layout_7.json'],
        ];
    }
}
