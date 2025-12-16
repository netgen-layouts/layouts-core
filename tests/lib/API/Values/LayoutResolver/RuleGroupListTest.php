<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleGroupList::class)]
final class RuleGroupListTest extends TestCase
{
    public function testGetRuleGroups(): void
    {
        $ruleGroups = [new RuleGroup(), new RuleGroup()];

        self::assertSame($ruleGroups, new RuleGroupList($ruleGroups)->getRuleGroups());
    }

    public function testGetRuleGroupIds(): void
    {
        $uuid1 = Uuid::v4();
        $uuid2 = Uuid::v4();

        $ruleGroups = [RuleGroup::fromArray(['id' => $uuid1]), RuleGroup::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], new RuleGroupList($ruleGroups)->getRuleGroupIds());
    }
}
