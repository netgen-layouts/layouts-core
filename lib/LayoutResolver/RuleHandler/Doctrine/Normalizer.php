<?php

namespace Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine;

class Normalizer
{
    /**
     * Normalizes rule data.
     *
     * @param array $data
     *
     * @return array
     */
    public function normalizeRules(array $data)
    {
        $rules = array();

        foreach ($data as $dataRow) {
            if (!isset($rules[$dataRow['id']])) {
                $rules[$dataRow['id']]['layout_id'] = (int)$dataRow['layout_id'];
                $rules[$dataRow['id']]['conditions'] = array();
            }

            if ($dataRow['condition_id'] === null) {
                continue;
            }

            if (!isset($rules[$dataRow['id']]['conditions'][$dataRow['condition_id']])) {
                $rules[$dataRow['id']]['conditions'][$dataRow['condition_id']]['identifier'] = $dataRow['identifier'];
                $rules[$dataRow['id']]['conditions'][$dataRow['condition_id']]['parameters'] = !empty($dataRow['parameters']) ?
                        json_decode($dataRow['parameters'], true) :
                        array();
            }
        }

        return $rules;
    }
}
