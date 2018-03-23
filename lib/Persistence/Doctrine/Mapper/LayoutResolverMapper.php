<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Mapper;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;

final class LayoutResolverMapper
{
    /**
     * Maps data from database to rule values.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule[]
     */
    public function mapRules(array $data = array())
    {
        $rules = array();

        foreach ($data as $dataItem) {
            $rules[] = new Rule(
                array(
                    'id' => (int) $dataItem['id'],
                    'status' => (int) $dataItem['status'],
                    'layoutId' => $dataItem['layout_id'] !== null ? (int) $dataItem['layout_id'] : null,
                    'enabled' => (bool) $dataItem['enabled'],
                    'priority' => (int) $dataItem['priority'],
                    'comment' => $dataItem['comment'],
                )
            );
        }

        return $rules;
    }

    /**
     * Maps data from database to target values.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target[]
     */
    public function mapTargets(array $data = array())
    {
        $targets = array();

        foreach ($data as $dataItem) {
            $targets[] = new Target(
                array(
                    'id' => (int) $dataItem['id'],
                    'status' => (int) $dataItem['status'],
                    'ruleId' => (int) $dataItem['rule_id'],
                    'type' => $dataItem['type'],
                    'value' => $dataItem['value'],
                )
            );
        }

        return $targets;
    }

    /**
     * Maps data from database to condition values.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition[]
     */
    public function mapConditions(array $data = array())
    {
        $conditions = array();

        foreach ($data as $dataItem) {
            $conditions[] = new Condition(
                array(
                    'id' => (int) $dataItem['id'],
                    'status' => (int) $dataItem['status'],
                    'ruleId' => (int) $dataItem['rule_id'],
                    'type' => $dataItem['type'],
                    'value' => json_decode($dataItem['value'], true),
                )
            );
        }

        return $conditions;
    }
}
