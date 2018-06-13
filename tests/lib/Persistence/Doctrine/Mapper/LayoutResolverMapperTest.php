<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class LayoutResolverMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new LayoutResolverMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapRules
     */
    public function testMapRules()
    {
        $data = [
            [
                'id' => 42,
                'layout_id' => 24,
                'enabled' => true,
                'priority' => 2,
                'comment' => 'Comment',
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'layout_id' => 25,
                'enabled' => false,
                'priority' => 3,
                'comment' => null,
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            new Rule(
                [
                    'id' => 42,
                    'layoutId' => 24,
                    'enabled' => true,
                    'priority' => 2,
                    'comment' => 'Comment',
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            new Rule(
                [
                    'id' => 43,
                    'layoutId' => 25,
                    'enabled' => false,
                    'priority' => 3,
                    'comment' => null,
                    'status' => Value::STATUS_DRAFT,
                ]
            ),
        ];

        $this->assertEquals($expectedData, $this->mapper->mapRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapTargets
     */
    public function testMapTargets()
    {
        $data = [
            [
                'id' => 42,
                'rule_id' => 1,
                'type' => 'target',
                'value' => '32',
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'rule_id' => 2,
                'type' => 'target2',
                'value' => '42',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            new Target(
                [
                    'id' => 42,
                    'ruleId' => 1,
                    'type' => 'target',
                    'value' => '32',
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            new Target(
                [
                    'id' => 43,
                    'ruleId' => 2,
                    'type' => 'target2',
                    'value' => '42',
                    'status' => Value::STATUS_DRAFT,
                ]
            ),
        ];

        $this->assertEquals($expectedData, $this->mapper->mapTargets($data));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapConditions
     */
    public function testMapConditions()
    {
        $data = [
            [
                'id' => 42,
                'rule_id' => 1,
                'type' => 'condition',
                'value' => '24',
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 43,
                'rule_id' => 2,
                'type' => 'condition2',
                'value' => '{"param":"value"}',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            new Condition(
                [
                    'id' => 42,
                    'ruleId' => 1,
                    'type' => 'condition',
                    'value' => '24',
                    'status' => Value::STATUS_PUBLISHED,
                ]
            ),
            new Condition(
                [
                    'id' => 43,
                    'ruleId' => 2,
                    'type' => 'condition2',
                    'value' => [
                        'param' => 'value',
                    ],
                    'status' => Value::STATUS_DRAFT,
                ]
            ),
        ];

        $this->assertEquals($expectedData, $this->mapper->mapConditions($data));
    }
}
