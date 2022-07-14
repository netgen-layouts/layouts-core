<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionList;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

use function sprintf;
use function str_replace;

final class ConditionListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\ConditionList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/(must be an instance of|must be of type) %s, (instance of )?%s given/',
                str_replace('\\', '\\\\', Condition::class),
                stdClass::class,
            ),
        );

        new ConditionList([new RuleCondition(), new stdClass(), new RuleCondition()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\ConditionList::__construct
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\ConditionList::getConditions
     */
    public function testGetConditions(): void
    {
        $conditions = [new RuleCondition(), new RuleCondition()];

        self::assertSame($conditions, (new ConditionList($conditions))->getConditions());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\ConditionList::getConditionIds
     */
    public function testGetConditionIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $conditions = [RuleCondition::fromArray(['id' => $uuid1]), RuleCondition::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], (new ConditionList($conditions))->getConditionIds());
    }
}
