<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionList;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(ConditionList::class)]
final class ConditionListTest extends TestCase
{
    public function testGetConditions(): void
    {
        $conditions = [new RuleCondition(), new RuleCondition()];

        self::assertSame($conditions, ConditionList::fromArray($conditions)->getConditions());
    }

    public function testGetConditionIds(): void
    {
        $uuid1 = Uuid::v4();
        $uuid2 = Uuid::v4();

        $conditions = [RuleCondition::fromArray(['id' => $uuid1]), RuleCondition::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], ConditionList::fromArray($conditions)->getConditionIds());
    }
}
