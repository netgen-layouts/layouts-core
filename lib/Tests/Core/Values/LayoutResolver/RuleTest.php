<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getLayout
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getPriority
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::isEnabled
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getComment
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getTargets
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getConditions
     */
    public function testSetDefaultProperties()
    {
        $rule = new Rule();

        $this->assertNull($rule->getId());
        $this->assertNull($rule->getStatus());
        $this->assertNull($rule->getLayout());
        $this->assertNull($rule->getPriority());
        $this->assertNull($rule->isEnabled());
        $this->assertNull($rule->getComment());
        $this->assertEquals(array(), $rule->getTargets());
        $this->assertEquals(array(), $rule->getConditions());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getLayout
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getPriority
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::isEnabled
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getComment
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getTargets
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getConditions
     */
    public function testSetProperties()
    {
        $rule = new Rule(
            array(
                'id' => 42,
                'status' => Rule::STATUS_PUBLISHED,
                'layout' => new LayoutInfo(array('id' => 24)),
                'priority' => 13,
                'enabled' => true,
                'comment' => 'Comment',
                'targets' => array(new Target(), new Target()),
                'conditions' => array(new Condition()),
            )
        );

        $this->assertEquals(42, $rule->getId());
        $this->assertEquals(Rule::STATUS_PUBLISHED, $rule->getStatus());
        $this->assertEquals(new LayoutInfo(array('id' => 24)), $rule->getLayout());
        $this->assertEquals(13, $rule->getPriority());
        $this->assertTrue($rule->isEnabled());
        $this->assertEquals('Comment', $rule->getComment());
        $this->assertCount(2, $rule->getTargets());
        $this->assertCount(1, $rule->getConditions());
    }
}
