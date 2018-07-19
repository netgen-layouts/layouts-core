<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Transfer\Output\Visitor\TargetVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class TargetVisitorTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->layoutResolverService = $this->createLayoutResolverService();
    }

    public function getVisitor(): VisitorInterface
    {
        return new TargetVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Target(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): APITarget { return $this->layoutResolverService->loadTarget(1); }, 'target/target_1.json'],
            [function (): APITarget { return $this->layoutResolverService->loadTarget(2); }, 'target/target_2.json'],
        ];
    }
}
