<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use function json_decode;

final class LayoutResolverMapper
{
    /**
     * Maps data from database to rule values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Rule[]
     */
    public function mapRules(array $data): array
    {
        $rules = [];

        foreach ($data as $dataItem) {
            $rules[] = Rule::fromArray(
                [
                    'id' => (int) $dataItem['id'],
                    'uuid' => $dataItem['uuid'],
                    'status' => (int) $dataItem['status'],
                    'ruleGroupId' => (int) $dataItem['rule_group_id'],
                    'layoutUuid' => $dataItem['layout_uuid'] ?? null,
                    'enabled' => (bool) $dataItem['enabled'],
                    'priority' => (int) $dataItem['priority'],
                    'comment' => $dataItem['comment'],
                ]
            );
        }

        return $rules;
    }

    /**
     * Maps data from database to rule group values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup[]
     */
    public function mapRuleGroups(array $data): array
    {
        $ruleGroups = [];

        foreach ($data as $dataItem) {
            $ruleGroups[] = RuleGroup::fromArray(
                [
                    'id' => (int) $dataItem['id'],
                    'uuid' => $dataItem['uuid'],
                    'status' => (int) $dataItem['status'],
                    'depth' => (int) $dataItem['depth'],
                    'path' => $dataItem['path'],
                    'parentId' => $dataItem['parent_id'] > 0 ? (int) $dataItem['parent_id'] : null,
                    'parentUuid' => $dataItem['parent_uuid'] ?? null,
                    'enabled' => (bool) $dataItem['enabled'],
                    'priority' => (int) $dataItem['priority'],
                    'comment' => $dataItem['comment'],
                ]
            );
        }

        return $ruleGroups;
    }

    /**
     * Maps data from database to target values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Target[]
     */
    public function mapTargets(array $data): array
    {
        $targets = [];

        foreach ($data as $dataItem) {
            $targets[] = Target::fromArray(
                [
                    'id' => (int) $dataItem['id'],
                    'uuid' => $dataItem['uuid'],
                    'status' => (int) $dataItem['status'],
                    'ruleId' => (int) $dataItem['rule_id'],
                    'ruleUuid' => $dataItem['rule_uuid'],
                    'type' => $dataItem['type'],
                    'value' => $dataItem['value'],
                ]
            );
        }

        return $targets;
    }

    /**
     * Maps data from database to condition values.
     *
     * @param mixed[] $data
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Condition[]
     */
    public function mapConditions(array $data): array
    {
        $conditions = [];

        foreach ($data as $dataItem) {
            $conditions[] = Condition::fromArray(
                [
                    'id' => (int) $dataItem['id'],
                    'uuid' => $dataItem['uuid'],
                    'status' => (int) $dataItem['status'],
                    'ruleId' => (int) $dataItem['rule_id'],
                    'ruleUuid' => $dataItem['rule_uuid'],
                    'type' => $dataItem['type'],
                    'value' => json_decode($dataItem['value'], true),
                ]
            );
        }

        return $conditions;
    }
}
