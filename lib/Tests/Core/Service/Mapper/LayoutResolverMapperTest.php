<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\API\Values\Page\LayoutInfo;
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
                'layoutId' => 1,
                'enabled' => true,
                'priority' => 12,
                'comment' => 'Comment',
            )
        );

        $rule = $this->layoutResolverMapper->mapRule($persistenceRule);

        $this->assertInstanceOf(APIRule::class, $rule);
        $this->assertEquals(3, $rule->getId());
        $this->assertInstanceOf(LayoutInfo::class, $rule->getLayout());
        $this->assertEquals(1, $rule->getLayout()->getId());
        $this->assertEquals(APIRule::STATUS_PUBLISHED, $rule->getStatus());
        $this->assertTrue($rule->isEnabled());
        $this->assertEquals(12, $rule->getPriority());
        $this->assertEquals('Comment', $rule->getComment());

        $this->assertNotEmpty($rule->getTargets());

        foreach ($rule->getTargets() as $target) {
            $this->assertInstanceOf(APITarget::class, $target);
        }

        $this->assertNotEmpty($rule->getConditions());

        foreach ($rule->getConditions() as $condition) {
            $this->assertInstanceOf(APICondition::class, $condition);
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

        $this->assertInstanceOf(APITarget::class, $target);
        $this->assertEquals(1, $target->getId());
        $this->assertEquals(APIRule::STATUS_PUBLISHED, $target->getStatus());
        $this->assertEquals(42, $target->getRuleId());
        $this->assertEquals('target', $target->getType());
        $this->assertEquals(42, $target->getValue());
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

        $this->assertInstanceOf(APICondition::class, $condition);
        $this->assertEquals(1, $condition->getId());
        $this->assertEquals(APIRule::STATUS_PUBLISHED, $condition->getStatus());
        $this->assertEquals(42, $condition->getRuleId());
        $this->assertEquals('condition', $condition->getType());
        $this->assertEquals(42, $condition->getValue());
    }
}
