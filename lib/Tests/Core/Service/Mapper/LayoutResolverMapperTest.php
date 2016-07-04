<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;

abstract class LayoutResolverMapperTest extends MapperTest
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    protected $layoutResolverMapper;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->layoutResolverMapper = $this->createLayoutResolverMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapRule
     */
    public function testMapRule()
    {
        $persistenceRule = new Rule(
            array(
                'id' => 3,
                'status' => APIRule::STATUS_PUBLISHED,
                'layoutId' => 42,
                'enabled' => true,
                'priority' => 12,
                'comment' => 'Comment',
            )
        );

        $rule = $this->layoutResolverMapper->mapRule($persistenceRule);

        self::assertInstanceOf(APIRule::class, $rule);
        self::assertEquals(3, $rule->getId());
        self::assertEquals(APIRule::STATUS_PUBLISHED, $rule->getStatus());
        self::assertEquals(42, $rule->getLayoutId());
        self::assertTrue($rule->isEnabled());
        self::assertEquals(12, $rule->getPriority());
        self::assertEquals('Comment', $rule->getComment());

        self::assertNotEmpty($rule->getTargets());

        foreach ($rule->getTargets() as $target) {
            self::assertInstanceOf(APITarget::class, $target);
        }

        self::assertNotEmpty($rule->getConditions());

        foreach ($rule->getConditions() as $condition) {
            self::assertInstanceOf(APICondition::class, $condition);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapTarget
     */
    public function testMapTarget()
    {
        $persistenceTarget = new Target(
            array(
                'id' => 1,
                'status' => APIRule::STATUS_PUBLISHED,
                'ruleId' => 42,
                'type' => 'target',
                'value' => 42,
            )
        );

        $target = $this->layoutResolverMapper->mapTarget($persistenceTarget);

        self::assertInstanceOf(APITarget::class, $target);
        self::assertEquals(1, $target->getId());
        self::assertEquals(APIRule::STATUS_PUBLISHED, $target->getStatus());
        self::assertEquals(42, $target->getRuleId());
        self::assertEquals('target', $target->getType());
        self::assertEquals(42, $target->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapCondition
     */
    public function testMapCondition()
    {
        $persistenceCondition = new Condition(
            array(
                'id' => 1,
                'status' => APIRule::STATUS_PUBLISHED,
                'ruleId' => 42,
                'type' => 'condition',
                'value' => 42,
            )
        );

        $condition = $this->layoutResolverMapper->mapCondition($persistenceCondition);

        self::assertInstanceOf(APICondition::class, $condition);
        self::assertEquals(1, $condition->getId());
        self::assertEquals(APIRule::STATUS_PUBLISHED, $condition->getStatus());
        self::assertEquals(42, $condition->getRuleId());
        self::assertEquals('condition', $condition->getType());
        self::assertEquals(42, $condition->getValue());
    }
}
