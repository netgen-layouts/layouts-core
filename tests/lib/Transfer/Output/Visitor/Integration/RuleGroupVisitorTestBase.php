<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Transfer\Output\Visitor\RuleGroupVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\LayoutResolver\RuleGroup>
 */
abstract class RuleGroupVisitorTestBase extends VisitorTestBase
{
    public function getVisitor(): VisitorInterface
    {
        return new RuleGroupVisitor($this->layoutResolverService);
    }

    public static function acceptDataProvider(): iterable
    {
        return [
            [new RuleGroup(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [fn (): RuleGroup => $this->layoutResolverService->loadRuleGroup(Uuid::fromString('00000000-0000-0000-0000-000000000000')), 'rule_group/rule_group_1.json'],
            [fn (): RuleGroup => $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')), 'rule_group/rule_group_2.json'],
        ];
    }
}
