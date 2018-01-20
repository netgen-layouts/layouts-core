<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use PHPUnit\Framework\TestCase;

final class RuleTest extends TestCase
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
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::isPublished
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
        $this->assertNull($rule->isPublished());
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
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::isPublished
     */
    public function testSetProperties()
    {
        $rule = new Rule(
            array(
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'layout' => new Layout(array('id' => 24)),
                'priority' => 13,
                'enabled' => true,
                'comment' => 'Comment',
                'targets' => array(new Target(), new Target()),
                'conditions' => array(new Condition()),
                'published' => true,
            )
        );

        $this->assertEquals(42, $rule->getId());
        $this->assertTrue($rule->isPublished());
        $this->assertEquals(new Layout(array('id' => 24)), $rule->getLayout());
        $this->assertEquals(13, $rule->getPriority());
        $this->assertTrue($rule->isEnabled());
        $this->assertEquals('Comment', $rule->getComment());
        $this->assertCount(2, $rule->getTargets());
        $this->assertCount(1, $rule->getConditions());
        $this->assertTrue($rule->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabled()
    {
        $rule = new Rule(
            array(
                'layout' => new Layout(array('id' => 24)),
                'targets' => array(new Target(), new Target()),
                'published' => true,
            )
        );

        $this->assertTrue($rule->canBeEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabledWhenNotPublished()
    {
        $rule = new Rule(
            array(
                'layout' => new Layout(array('id' => 24)),
                'targets' => array(new Target(), new Target()),
                'published' => false,
            )
        );

        $this->assertFalse($rule->canBeEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabledWithNoLayout()
    {
        $rule = new Rule(
            array(
                'layout' => null,
                'targets' => array(new Target(), new Target()),
                'published' => true,
            )
        );

        $this->assertFalse($rule->canBeEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabledWithNoTargets()
    {
        $rule = new Rule(
            array(
                'layout' => new Layout(array('id' => 24)),
                'targets' => array(),
                'published' => true,
            )
        );

        $this->assertFalse($rule->canBeEnabled());
    }
}
