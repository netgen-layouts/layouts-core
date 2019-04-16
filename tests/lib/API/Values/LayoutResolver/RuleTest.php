<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\Value;
use PHPUnit\Framework\TestCase;

final class RuleTest extends TestCase
{
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new Rule());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::__construct
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::getConditions
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::getTargets
     */
    public function testSetDefaultProperties(): void
    {
        $rule = new Rule();

        self::assertCount(0, $rule->getTargets());
        self::assertCount(0, $rule->getConditions());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::__construct
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::getComment
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::getConditions
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::getId
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::getLayout
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::getPriority
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::getTargets
     * @covers \Netgen\BlockManager\API\Values\LayoutResolver\Rule::isEnabled
     */
    public function testSetProperties(): void
    {
        $target1 = new Target();
        $target2 = new Target();

        $condition = new Condition();

        $layout = Layout::fromArray(['id' => 24]);

        $rule = Rule::fromArray(
            [
                'id' => 42,
                'layout' => $layout,
                'priority' => 13,
                'enabled' => true,
                'comment' => 'Comment',
                'targets' => new ArrayCollection([$target1, $target2]),
                'conditions' => new ArrayCollection([$condition]),
            ]
        );

        self::assertSame(42, $rule->getId());
        self::assertSame($layout, $rule->getLayout());
        self::assertSame(13, $rule->getPriority());
        self::assertTrue($rule->isEnabled());
        self::assertSame('Comment', $rule->getComment());

        self::assertCount(2, $rule->getTargets());
        self::assertCount(1, $rule->getConditions());

        self::assertSame($target1, $rule->getTargets()[0]);
        self::assertSame($target2, $rule->getTargets()[1]);

        self::assertSame($condition, $rule->getConditions()[0]);
    }
}
