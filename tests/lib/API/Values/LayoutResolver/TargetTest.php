<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use PHPUnit\Framework\TestCase;

final class TargetTest extends TestCase
{
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new Target());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Target::getId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Target::getRuleId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Target::getTargetType
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Target::getValue
     */
    public function testSetProperties(): void
    {
        $targetType = new TargetType1();

        $target = Target::fromArray(
            [
                'id' => 42,
                'ruleId' => 30,
                'targetType' => $targetType,
                'value' => 32,
            ]
        );

        self::assertSame(42, $target->getId());
        self::assertSame(30, $target->getRuleId());
        self::assertSame($targetType, $target->getTargetType());
        self::assertSame(32, $target->getValue());
    }
}
