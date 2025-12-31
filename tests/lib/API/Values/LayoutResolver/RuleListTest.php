<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RuleList::class)]
final class RuleListTest extends TestCase
{
    public function testGetRules(): void
    {
        $rules = [new Rule(), new Rule()];

        self::assertSame($rules, RuleList::fromArray($rules)->getRules());
    }

    public function testGetRuleIds(): void
    {
        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();

        $rules = [Rule::fromArray(['id' => $uuid1]), Rule::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], RuleList::fromArray($rules)->getRuleIds());
    }
}
