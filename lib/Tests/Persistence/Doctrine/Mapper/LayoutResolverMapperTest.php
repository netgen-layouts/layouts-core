<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;

class LayoutResolverMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new LayoutResolverMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapRules
     */
    public function testMapRules()
    {
        $data = array(
            array(
                'id' => 42,
                'layout_id' => 24,
                'enabled' => true,
                'priority' => 2,
                'comment' => 'Comment',
                'status' => Rule::STATUS_PUBLISHED,
            ),
            array(
                'id' => 43,
                'layout_id' => 25,
                'enabled' => false,
                'priority' => 3,
                'comment' => null,
                'status' => Rule::STATUS_DRAFT,
            ),
        );

        $expectedData = array(
            new Rule(
                array(
                    'id' => 42,
                    'layoutId' => 24,
                    'enabled' => true,
                    'priority' => 2,
                    'comment' => 'Comment',
                    'status' => Rule::STATUS_PUBLISHED,
                )
            ),
            new Rule(
                array(
                    'id' => 43,
                    'layoutId' => 25,
                    'enabled' => false,
                    'priority' => 3,
                    'comment' => null,
                    'status' => Rule::STATUS_DRAFT,
                )
            ),
        );

        self::assertEquals($expectedData, $this->mapper->mapRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapTargets
     */
    public function testMapTargets()
    {
        $data = array(
            array(
                'id' => 42,
                'rule_id' => 1,
                'identifier' => 'target',
                'value' => '32',
                'status' => Rule::STATUS_PUBLISHED,
            ),
            array(
                'id' => 43,
                'rule_id' => 2,
                'identifier' => 'target2',
                'value' => '42',
                'status' => Rule::STATUS_DRAFT,
            ),
        );

        $expectedData = array(
            new Target(
                array(
                    'id' => 42,
                    'ruleId' => 1,
                    'identifier' => 'target',
                    'value' => '32',
                    'status' => Rule::STATUS_PUBLISHED,
                )
            ),
            new Target(
                array(
                    'id' => 43,
                    'ruleId' => 2,
                    'identifier' => 'target2',
                    'value' => '42',
                    'status' => Rule::STATUS_DRAFT,
                )
            ),
        );

        self::assertEquals($expectedData, $this->mapper->mapTargets($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapConditions
     */
    public function testMapConditions()
    {
        $data = array(
            array(
                'id' => 42,
                'rule_id' => 1,
                'identifier' => 'condition',
                'value' => '24',
                'status' => Rule::STATUS_PUBLISHED,
            ),
            array(
                'id' => 43,
                'rule_id' => 2,
                'identifier' => 'condition2',
                'value' => '{"param":"value"}',
                'status' => Rule::STATUS_DRAFT,
            ),
        );

        $expectedData = array(
            new Condition(
                array(
                    'id' => 42,
                    'ruleId' => 1,
                    'identifier' => 'condition',
                    'value' => '24',
                    'status' => Rule::STATUS_PUBLISHED,
                )
            ),
            new Condition(
                array(
                    'id' => 43,
                    'ruleId' => 2,
                    'identifier' => 'condition2',
                    'value' => array(
                        'param' => 'value',
                    ),
                    'status' => Rule::STATUS_DRAFT,
                )
            ),
        );

        self::assertEquals($expectedData, $this->mapper->mapConditions($data));
    }
}
