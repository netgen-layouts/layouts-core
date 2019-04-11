<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\Transfer\Output\Visitor\ConditionVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

abstract class ConditionVisitorTest extends VisitorTest
{
    public function getVisitor(): VisitorInterface
    {
        return new ConditionVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Condition(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): Condition { return $this->layoutResolverService->loadCondition(1); }, 'condition/condition_1.json'],
            [function (): Condition { return $this->layoutResolverService->loadCondition(2); }, 'condition/condition_2.json'],
        ];
    }
}
