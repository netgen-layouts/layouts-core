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
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTest<\Netgen\Layouts\API\Values\LayoutResolver\RuleGroup>
 */
abstract class RuleGroupVisitorTest extends VisitorTest
{
    public function getVisitor(): VisitorInterface
    {
        return new RuleGroupVisitor($this->layoutResolverService);
    }

    public function acceptDataProvider(): array
    {
        return [
            [new RuleGroup(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitDataProvider(): array
    {
        return [
            [fn (): RuleGroup => $this->layoutResolverService->loadRuleGroup(Uuid::fromString('00000000-0000-0000-0000-000000000000')), 'rule_group/rule_group_1.json'],
            [fn (): RuleGroup => $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')), 'rule_group/rule_group_2.json'],
        ];
    }
}
