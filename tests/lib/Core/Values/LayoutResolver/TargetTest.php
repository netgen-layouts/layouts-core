<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use PHPUnit\Framework\TestCase;

final class TargetTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Target());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getTargetType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getValue
     */
    public function testSetProperties(): void
    {
        $targetType = new TargetType('target');

        $target = Target::fromArray(
            [
                'id' => 42,
                'ruleId' => 30,
                'targetType' => $targetType,
                'value' => 32,
            ]
        );

        $this->assertSame(42, $target->getId());
        $this->assertSame(30, $target->getRuleId());
        $this->assertSame($targetType, $target->getTargetType());
        $this->assertSame(32, $target->getValue());
    }
}
