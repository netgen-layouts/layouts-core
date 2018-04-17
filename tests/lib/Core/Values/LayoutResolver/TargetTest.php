<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use PHPUnit\Framework\TestCase;

final class TargetTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getTargetType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getValue
     */
    public function testSetDefaultProperties()
    {
        $target = new Target();

        $this->assertNull($target->getId());
        $this->assertNull($target->getStatus());
        $this->assertNull($target->getRuleId());
        $this->assertNull($target->getTargetType());
        $this->assertNull($target->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getTargetType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getValue
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::isPublished
     */
    public function testSetProperties()
    {
        $target = new Target(
            [
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 30,
                'targetType' => new TargetType('target'),
                'value' => 32,
            ]
        );

        $this->assertEquals(42, $target->getId());
        $this->assertTrue($target->isPublished());
        $this->assertEquals(30, $target->getRuleId());
        $this->assertEquals(new TargetType('target'), $target->getTargetType());
        $this->assertEquals(32, $target->getValue());
        $this->assertTrue($target->isPublished());
    }
}
