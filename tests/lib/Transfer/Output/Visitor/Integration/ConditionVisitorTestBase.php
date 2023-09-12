<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Transfer\Output\Visitor\ConditionVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\LayoutResolver\Condition>
 */
abstract class ConditionVisitorTestBase extends VisitorTestBase
{
    public function getVisitor(): VisitorInterface
    {
        return new ConditionVisitor();
    }

    public static function acceptDataProvider(): iterable
    {
        return [
            [new RuleCondition(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [fn (): RuleCondition => $this->layoutResolverService->loadRuleCondition(Uuid::fromString('35f4594c-6674-5815-add6-07f288b79686')), 'condition/condition_1.json'],
            [fn (): RuleCondition => $this->layoutResolverService->loadRuleCondition(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca')), 'condition/condition_2.json'],
        ];
    }
}
