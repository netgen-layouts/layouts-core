<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class RuleGroupTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getConditions
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getDescription
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup::getName
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
                'name' => 'Name',
                'description' => 'Description',
                'priority' => 13,
                'enabled' => true,
                'rules' => new ArrayCollection([$rule1, $rule2]),
                'conditions' => new ArrayCollection([$condition]),
            ],
        );

        self::assertSame($uuid->toString(), $ruleGroup->getId()->toString());
        self::assertInstanceOf(UuidInterface::class, $ruleGroup->getParentId());
        self::assertSame($parentUuid->toString(), $ruleGroup->getParentId()->toString());
        self::assertSame('Name', $ruleGroup->getName());
        self::assertSame('Description', $ruleGroup->getDescription());
        self::assertSame(13, $ruleGroup->getPriority());
        self::assertTrue($ruleGroup->isEnabled());

        self::assertCount(2, $ruleGroup->getRules());
        self::assertCount(1, $ruleGroup->getConditions());

        self::assertSame($rule1, $ruleGroup->getRules()[0]);
        self::assertSame($rule2, $ruleGroup->getRules()[1]);

        self::assertSame($condition, $ruleGroup->getConditions()[0]);
    }
}
