<?php

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target as TargetValue;
use Netgen\BlockManager\Transfer\Output\Visitor\Target;

abstract class TargetTest extends VisitorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->layoutResolverService = $this->createLayoutResolverService();
    }

    public function getVisitor()
    {
        return new Target();
    }

    public function acceptProvider()
    {
        return [
            [new TargetValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider()
    {
        return [
            [function () { return $this->layoutResolverService->loadTarget(1); }, 'target/target_1.json'],
            [function () { return $this->layoutResolverService->loadTarget(2); }, 'target/target_2.json'],
        ];
    }
}
