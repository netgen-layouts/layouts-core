<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Rule::getConditions
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Rule::getDescription
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Rule::getId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Rule::getLayout
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Rule::getPriority
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Rule::getRuleGroupId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Rule::getTargets
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Rule::isEnabled
     */
    public function testSetProperties(): void
    {
        $target1 = new Target();
        $target2 = new Target();

        $condition = new RuleCondition();

        $layout = new Layout();

        $uuid = Uuid::uuid4();
        $ruleGroupUuid = Uuid::uuid4();

        $rule = Rule::fromArray(
            [
                'id' => $uuid,
                'ruleGroupId' => $ruleGroupUuid,
                'layout' => $layout,
                'priority' => 13,
                'enabled' => true,
                'description' => 'Description',
                'targets' => new ArrayCollection([$target1, $target2]),
                'conditions' => new ArrayCollection([$condition]),
            ],
        );

        self::assertSame($uuid->toString(), $rule->getId()->toString());
        self::assertSame($ruleGroupUuid->toString(), $rule->getRuleGroupId()->toString());
        self::assertSame($layout, $rule->getLayout());
        self::assertSame(13, $rule->getPriority());
        self::assertTrue($rule->isEnabled());
        self::assertSame('Description', $rule->getDescription());

        self::assertCount(2, $rule->getTargets());
        self::assertCount(1, $rule->getConditions());

        self::assertSame($target1, $rule->getTargets()[0]);
        self::assertSame($target2, $rule->getTargets()[1]);

        self::assertSame($condition, $rule->getConditions()[0]);
    }
}
