<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use PHPUnit\Framework\TestCase;

class TargetTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getValue
     */
    public function testSetDefaultProperties()
    {
        $target = new Target();

        $this->assertNull($target->getId());
        $this->assertNull($target->getStatus());
        $this->assertNull($target->getRuleId());
        $this->assertNull($target->getType());
        $this->assertNull($target->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Target::getValue
     */
    public function testSetProperties()
    {
        $target = new Target(
            array(
                'id' => 42,
                'status' => Rule::STATUS_PUBLISHED,
                'ruleId' => 30,
                'type' => 'target',
                'value' => 32,
            )
        );

        $this->assertEquals(42, $target->getId());
        $this->assertEquals(Rule::STATUS_PUBLISHED, $target->getStatus());
        $this->assertEquals(30, $target->getRuleId());
        $this->assertEquals('target', $target->getType());
        $this->assertEquals(32, $target->getValue());
    }
}
