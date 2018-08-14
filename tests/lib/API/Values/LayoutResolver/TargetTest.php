<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType1;
use PHPUnit\Framework\TestCase;

final class TargetTest extends TestCase
{
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new Target());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Target::getId
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Target::getRuleId
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Target::getTargetType
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Target::getValue
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
