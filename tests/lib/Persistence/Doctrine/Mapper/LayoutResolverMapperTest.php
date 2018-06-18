<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\TestCase\ExportObjectVarsTrait;
use PHPUnit\Framework\TestCase;

final class LayoutResolverMapperTest extends TestCase
{
    use ExportObjectVarsTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new LayoutResolverMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapRules
     */
    public function testMapRules(): void
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
            [
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'layoutId' => 24,
                'enabled' => true,
                'priority' => 2,
                'comment' => 'Comment',
            ],
            [
                'id' => 43,
                'status' => Value::STATUS_DRAFT,
                'layoutId' => 25,
                'enabled' => false,
                'priority' => 3,
                'comment' => null,
            ],
        ];

        $rules = $this->mapper->mapRules($data);

        foreach ($rules as $rule) {
            $this->assertInstanceOf(Rule::class, $rule);
        }

        $this->assertSame($expectedData, $this->exportObjectArrayVars($rules));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapTargets
     */
    public function testMapTargets(): void
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
            [
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 1,
                'type' => 'target',
                'value' => '32',
            ],
            [
                'id' => 43,
                'status' => Value::STATUS_DRAFT,
                'ruleId' => 2,
                'type' => 'target2',
                'value' => '42',
            ],
        ];

        $targets = $this->mapper->mapTargets($data);

        foreach ($targets as $target) {
            $this->assertInstanceOf(Target::class, $target);
        }

        $this->assertSame($expectedData, $this->exportObjectArrayVars($targets));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapConditions
     */
    public function testMapConditions(): void
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
            [
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 1,
                'type' => 'condition',
                'value' => 24,
            ],
            [
                'id' => 43,
                'status' => Value::STATUS_DRAFT,
                'ruleId' => 2,
                'type' => 'condition2',
                'value' => [
                    'param' => 'value',
                ],
            ],
        ];

        $conditions = $this->mapper->mapConditions($data);

        foreach ($conditions as $condition) {
            $this->assertInstanceOf(Condition::class, $condition);
        }

        $this->assertSame($expectedData, $this->exportObjectArrayVars($conditions));
    }
}
