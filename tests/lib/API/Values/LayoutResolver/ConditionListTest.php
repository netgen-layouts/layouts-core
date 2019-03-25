<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class ConditionListTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\ConditionList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                ConditionList::class,
                str_replace('\ConditionList', '', ConditionList::class),
                Condition::class,
                stdClass::class
            )
        );

        new ConditionList([new Condition(), new stdClass(), new Condition()]);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\ConditionList::__construct
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\ConditionList::getConditions
     */
    public function testGetConditions(): void
    {
        $conditions = [new Condition(), new Condition()];

        self::assertSame($conditions, (new ConditionList($conditions))->getConditions());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\ConditionList::getConditionIds
     */
    public function testGetConditionIds(): void
    {
        $conditions = [Condition::fromArray(['id' => 42]), Condition::fromArray(['id' => 24])];

        self::assertSame([42, 24], (new ConditionList($conditions))->getConditionIds());
    }
}
