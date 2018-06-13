<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition as ConditionValue;
use Netgen\BlockManager\Transfer\Output\Visitor\Condition;

abstract class ConditionTest extends VisitorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->layoutResolverService = $this->createLayoutResolverService();
    }

    public function getVisitor()
    {
        return new Condition();
    }

    public function acceptProvider()
    {
        return [
            [new ConditionValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider()
    {
        return [
            [function () { return $this->layoutResolverService->loadCondition(1); }, 'condition/condition_1.json'],
            [function () { return $this->layoutResolverService->loadCondition(2); }, 'condition/condition_2.json'],
        ];
    }
}
