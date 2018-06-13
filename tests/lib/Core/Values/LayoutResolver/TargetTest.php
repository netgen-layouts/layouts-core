<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use PHPUnit\Framework\TestCase;

final class TargetTest extends TestCase
{
    public function testInstance()
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
    public function testSetProperties()
    {
        $target = new Target(
            [
                'id' => 42,
                'ruleId' => 30,
                'targetType' => new TargetType('target'),
                'value' => 32,
            ]
        );

        $this->assertEquals(42, $target->getId());
        $this->assertEquals(30, $target->getRuleId());
        $this->assertEquals(new TargetType('target'), $target->getTargetType());
        $this->assertEquals(32, $target->getValue());
    }
}
