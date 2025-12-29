<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Transfer\Output\Visitor\RuleGroupVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\LayoutResolver\RuleGroup>
 */
abstract class RuleGroupVisitorTestBase extends VisitorTestBase
{
    final public static function acceptDataProvider(): iterable
    {
        return [
            [new RuleGroup(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['rule_group/rule_group_1.json', '00000000-0000-0000-0000-000000000000'],
            ['rule_group/rule_group_2.json', 'b4f85f38-de3f-4af7-9a5f-21df63a49da9'],
        ];
    }

    final protected function getVisitor(): VisitorInterface
    {
        return new RuleGroupVisitor($this->layoutResolverService);
    }

    final protected function loadValue(string $id, string ...$additionalParameters): RuleGroup
    {
        return $this->layoutResolverService->loadRuleGroup(Uuid::fromString($id));
    }
}
