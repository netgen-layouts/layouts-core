<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Core\Values\Block\Placeholder as PlaceholderValue;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Transfer\Output\Visitor\Placeholder;

abstract class PlaceholderTest extends VisitorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor()
    {
        $this->getVisitor()->visit(new PlaceholderValue());
    }

    public function getVisitor()
    {
        return new Placeholder();
    }

    public function acceptProvider()
    {
        return [
            [new PlaceholderValue(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    public function visitProvider()
    {
        return [
            [function () { return $this->blockService->loadBlock(33)->getPlaceholder('left'); }, 'placeholder/block_33_left.json'],
            [function () { return $this->blockService->loadBlock(33)->getPlaceholder('right'); }, 'placeholder/block_33_right.json'],
        ];
    }
}
