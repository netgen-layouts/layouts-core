<?php

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\Layout\Zone as ZoneValue;
use Netgen\BlockManager\Transfer\Output\Visitor\Zone;

abstract class ZoneTest extends VisitorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();
        $this->layoutService = $this->createLayoutService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor()
    {
        $this->getVisitor()->visit(new ZoneValue());
    }

    public function getVisitor()
    {
        return new Zone($this->blockService);
    }

    public function acceptProvider()
    {
        return [
            [new ZoneValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider()
    {
        return [
            [function () { return $this->layoutService->loadZone(2, 'top'); }, 'zone/zone_2_top.json'],
            [function () { return $this->layoutService->loadZone(2, 'right'); }, 'zone/zone_2_right.json'],
            [function () { return $this->layoutService->loadZone(6, 'bottom'); }, 'zone/zone_6_bottom.json'],
        ];
    }
}
