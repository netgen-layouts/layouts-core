<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getComment
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getConditions
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getLayout
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getPriority
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getTargets
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::isEnabled
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
        $this->assertEquals([], $rule->getTargets());
        $this->assertEquals([], $rule->getConditions());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getComment
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getConditions
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getLayout
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getPriority
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getTargets
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::isEnabled
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::isPublished
     */
    public function testSetProperties()
    {
        $rule = new Rule(
            [
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'layout' => new Layout(['id' => 24]),
                'priority' => 13,
                'enabled' => true,
                'comment' => 'Comment',
                'targets' => new ArrayCollection([new Target(), new Target()]),
                'conditions' => new ArrayCollection([new Condition()]),
            ]
        );

        $this->assertEquals(42, $rule->getId());
        $this->assertTrue($rule->isPublished());
        $this->assertEquals(new Layout(['id' => 24]), $rule->getLayout());
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
            [
                'layout' => new Layout(['id' => 24]),
                'targets' => new ArrayCollection([new Target(), new Target()]),
                'status' => Rule::STATUS_PUBLISHED,
            ]
        );

        $this->assertTrue($rule->canBeEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabledWhenNotPublished()
    {
        $rule = new Rule(
            [
                'layout' => new Layout(['id' => 24]),
                'targets' => new ArrayCollection([new Target(), new Target()]),
                'status' => Rule::STATUS_DRAFT,
            ]
        );

        $this->assertFalse($rule->canBeEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabledWithNoLayout()
    {
        $rule = new Rule(
            [
                'layout' => null,
                'targets' => new ArrayCollection([new Target(), new Target()]),
                'status' => Rule::STATUS_PUBLISHED,
            ]
        );

        $this->assertFalse($rule->canBeEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabledWithNoTargets()
    {
        $rule = new Rule(
            [
                'layout' => new Layout(['id' => 24]),
                'targets' => new ArrayCollection(),
                'status' => Rule::STATUS_PUBLISHED,
            ]
        );

        $this->assertFalse($rule->canBeEnabled());
    }
}
