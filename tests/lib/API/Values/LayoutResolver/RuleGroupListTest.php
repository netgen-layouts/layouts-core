<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

use function sprintf;
use function str_replace;

final class RuleGroupListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/(must be an instance of|must be of type) %s, (instance of )?%s given/',
                str_replace('\\', '\\\\', RuleGroup::class),
                stdClass::class,
            ),
        );

        new RuleGroupList([new RuleGroup(), new stdClass(), new RuleGroup()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList::__construct
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList::getRuleGroups
     */
    public function testGetRuleGroups(): void
    {
        $ruleGroups = [new RuleGroup(), new RuleGroup()];

        self::assertSame($ruleGroups, (new RuleGroupList($ruleGroups))->getRuleGroups());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList::getRuleGroupIds
     */
    public function testGetRuleGroupIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $ruleGroups = [RuleGroup::fromArray(['id' => $uuid1]), RuleGroup::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], (new RuleGroupList($ruleGroups))->getRuleGroupIds());
    }
}
