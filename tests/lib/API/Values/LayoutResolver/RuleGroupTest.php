<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\API\Values\Value;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class RuleGroupTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::__construct
     */
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new RuleGroup());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getConditions
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getRules
     */
    public function testSetDefaultProperties(): void
    {
        $ruleGroup = new RuleGroup();

        self::assertCount(0, $ruleGroup->getRules());
        self::assertCount(0, $ruleGroup->getConditions());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getComment
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getConditions
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getParentId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getPriority
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getRules
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::isEnabled
     */
    public function testSetProperties(): void
    {
        $rule1 = new Rule();
        $rule2 = new Rule();

        $condition = new RuleGroupCondition();

        $uuid = Uuid::uuid4();
        $parentUuid = Uuid::uuid4();

        $ruleGroup = RuleGroup::fromArray(
            [
                'id' => $uuid,
                'parentId' => $parentUuid,
                'priority' => 13,
                'enabled' => true,
                'comment' => 'Comment',
                'rules' => new ArrayCollection([$rule1, $rule2]),
                'conditions' => new ArrayCollection([$condition]),
            ]
        );

        self::assertSame($uuid->toString(), $ruleGroup->getId()->toString());
        self::assertInstanceOf(UuidInterface::class, $ruleGroup->getParentId());
        self::assertSame($parentUuid->toString(), $ruleGroup->getParentId()->toString());
        self::assertSame(13, $ruleGroup->getPriority());
        self::assertTrue($ruleGroup->isEnabled());
        self::assertSame('Comment', $ruleGroup->getComment());

        self::assertCount(2, $ruleGroup->getRules());
        self::assertCount(1, $ruleGroup->getConditions());

        self::assertSame($rule1, $ruleGroup->getRules()[0]);
        self::assertSame($rule2, $ruleGroup->getRules()[1]);

        self::assertSame($condition, $ruleGroup->getConditions()[0]);
    }
}
