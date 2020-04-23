<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionList;
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
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\ConditionList::__construct
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\ConditionList::getConditions
     */
    public function testGetConditions(): void
    {
        $conditions = [new Condition(), new Condition()];

        self::assertSame($conditions, (new ConditionList($conditions))->getConditions());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\ConditionList::getConditionIds
     */
    public function testGetConditionIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $conditions = [Condition::fromArray(['id' => $uuid1]), Condition::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], (new ConditionList($conditions))->getConditionIds());
    }
}
