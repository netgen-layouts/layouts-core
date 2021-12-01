<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class LayoutResolverMapperTest extends TestCase
{
    use ExportObjectTrait;

    private LayoutResolverMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new LayoutResolverMapper();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapRules
     */
    public function testMapRules(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'rule_group_id' => '62',
                'enabled' => '1',
                'priority' => '2',
                'description' => 'Description',
                'status' => '1',
                'layout_uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
            [
                'id' => '43',
                'uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'rule_group_id' => '64',
                'enabled' => '0',
                'priority' => '3',
                'description' => '',
                'status' => Value::STATUS_DRAFT,
                'layout_uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
            ],
        ];

        $expectedData = [
            [
                'description' => 'Description',
                'enabled' => true,
                'id' => 42,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'priority' => 2,
                'ruleGroupId' => 62,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
            ],
            [
                'description' => '',
                'enabled' => false,
                'id' => 43,
                'layoutUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'priority' => 3,
                'ruleGroupId' => 64,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
            ],
        ];

        $rules = $this->mapper->mapRules($data);

        self::assertContainsOnlyInstancesOf(Rule::class, $rules);
        self::assertSame($expectedData, $this->exportObjectList($rules));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapRuleGroups
     */
    public function testMapRuleGroups(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'depth' => 1,
                'path' => '/62/43/',
                'parent_id' => '62',
                'parent_uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'name' => 'Name',
                'description' => 'Description',
                'enabled' => '1',
                'priority' => '2',
                'status' => '1',
            ],
            [
                'id' => '43',
                'uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'depth' => 0,
                'path' => '/43/',
                'parent_id' => null,
                'parent_uuid' => null,
                'name' => '',
                'description' => '',
                'enabled' => '0',
                'priority' => '3',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            [
                'depth' => 1,
                'description' => 'Description',
                'enabled' => true,
                'id' => 42,
                'name' => 'Name',
                'parentId' => 62,
                'parentUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'path' => '/62/43/',
                'priority' => 2,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
            ],
            [
                'depth' => 0,
                'description' => '',
                'enabled' => false,
                'id' => 43,
                'name' => '',
                'parentId' => null,
                'parentUuid' => null,
                'path' => '/43/',
                'priority' => 3,
                'status' => Value::STATUS_DRAFT,
                'uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
            ],
        ];

        $ruleGroups = $this->mapper->mapRuleGroups($data);

        self::assertContainsOnlyInstancesOf(RuleGroup::class, $ruleGroups);
        self::assertSame($expectedData, $this->exportObjectList($ruleGroups));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapTargets
     */
    public function testMapTargets(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'rule_id' => '1',
                'rule_uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'type' => 'target',
                'value' => '32',
                'status' => '1',
            ],
            [
                'id' => 43,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'rule_id' => 2,
                'rule_uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'type' => 'target2',
                'value' => '42',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'ruleId' => 1,
                'ruleUuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'target',
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'value' => '32',
            ],
            [
                'id' => 43,
                'ruleId' => 2,
                'ruleUuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'status' => Value::STATUS_DRAFT,
                'type' => 'target2',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => '42',
            ],
        ];

        $targets = $this->mapper->mapTargets($data);

        self::assertContainsOnlyInstancesOf(Target::class, $targets);
        self::assertSame($expectedData, $this->exportObjectList($targets));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapRuleConditions
     */
    public function testMapRuleConditions(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'rule_id' => '1',
                'rule_uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'type' => 'condition',
                'value' => '24',
                'status' => '1',
            ],
            [
                'id' => 43,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'rule_id' => 2,
                'rule_uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'type' => 'condition2',
                'value' => '{"param":"value"}',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'ruleId' => 1,
                'ruleUuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'condition',
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'value' => 24,
            ],
            [
                'id' => 43,
                'ruleId' => 2,
                'ruleUuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'status' => Value::STATUS_DRAFT,
                'type' => 'condition2',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => [
                    'param' => 'value',
                ],
            ],
        ];

        $conditions = $this->mapper->mapRuleConditions($data);

        self::assertContainsOnlyInstancesOf(RuleCondition::class, $conditions);
        self::assertSame($expectedData, $this->exportObjectList($conditions));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\LayoutResolverMapper::mapRuleGroupConditions
     */
    public function testMapRuleGroupConditions(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'rule_group_id' => '1',
                'rule_group_uuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'type' => 'condition',
                'value' => '24',
                'status' => '1',
            ],
            [
                'id' => 43,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'rule_group_id' => 2,
                'rule_group_uuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'type' => 'condition2',
                'value' => '{"param":"value"}',
                'status' => Value::STATUS_DRAFT,
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'ruleGroupId' => 1,
                'ruleGroupUuid' => '02a720f4-1083-58f5-bb23-7067c3451b19',
                'status' => Value::STATUS_PUBLISHED,
                'type' => 'condition',
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'value' => 24,
            ],
            [
                'id' => 43,
                'ruleGroupId' => 2,
                'ruleGroupUuid' => '92bc1d5d-0016-5510-a095-65e218db0adf',
                'status' => Value::STATUS_DRAFT,
                'type' => 'condition2',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => [
                    'param' => 'value',
                ],
            ],
        ];

        $conditions = $this->mapper->mapRuleGroupConditions($data);

        self::assertContainsOnlyInstancesOf(RuleGroupCondition::class, $conditions);
        self::assertSame($expectedData, $this->exportObjectList($conditions));
    }
}
