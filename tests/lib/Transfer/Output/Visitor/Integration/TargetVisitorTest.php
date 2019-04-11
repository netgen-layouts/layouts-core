<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\Transfer\Output\Visitor\TargetVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

abstract class TargetVisitorTest extends VisitorTest
{
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
            [function (): Target { return $this->layoutResolverService->loadTarget(1); }, 'target/target_1.json'],
            [function (): Target { return $this->layoutResolverService->loadTarget(2); }, 'target/target_2.json'],
        ];
    }
}
