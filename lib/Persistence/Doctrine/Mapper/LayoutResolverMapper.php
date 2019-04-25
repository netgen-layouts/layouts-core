<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;

final class LayoutResolverMapper
{
    /**
     * Maps data from database to rule values.
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
                    'layoutId' => $dataItem['layout_id'] !== null ? (int) $dataItem['layout_id'] : null,
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
     * Maps data from database to target values.
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
