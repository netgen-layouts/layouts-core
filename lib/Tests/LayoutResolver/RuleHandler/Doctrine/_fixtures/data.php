<?php

return array(
    'ngbm_rule' => array(
        array('id' => 1, 'layout_id' => 1, 'target_identifier' => 'route'),
        array('id' => 2, 'layout_id' => 2, 'target_identifier' => 'route'),
        array('id' => 3, 'layout_id' => 3, 'target_identifier' => 'route'),
        array('id' => 4, 'layout_id' => 1, 'target_identifier' => 'route_prefix'),
        array('id' => 5, 'layout_id' => 2, 'target_identifier' => 'route_prefix'),
        array('id' => 6, 'layout_id' => 3, 'target_identifier' => 'route_prefix'),
    ),
    'ngbm_rule_value' => array(
        array('id' => 1, 'rule_id' => 1, 'value' => 'my_cool_route'),
        array('id' => 2, 'rule_id' => 1, 'value' => 'my_other_cool_route'),
        array('id' => 3, 'rule_id' => 2, 'value' => 'my_second_cool_route'),
        array('id' => 4, 'rule_id' => 2, 'value' => 'my_third_cool_route'),
        array('id' => 5, 'rule_id' => 3, 'value' => 'my_fourth_cool_route'),
        array('id' => 6, 'rule_id' => 3, 'value' => 'my_fifth_cool_route'),
        array('id' => 7, 'rule_id' => 4, 'value' => 'my_cool_'),
        array('id' => 8, 'rule_id' => 4, 'value' => 'my_other_cool_'),
        array('id' => 9, 'rule_id' => 5, 'value' => 'my_second_cool_'),
        array('id' => 10, 'rule_id' => 5, 'value' => 'my_third_cool_'),
        array('id' => 11, 'rule_id' => 6, 'value' => 'my_fourth_cool_'),
        array('id' => 12, 'rule_id' => 6, 'value' => 'my_fifth_cool_'),
    ),
    'ngbm_rule_condition' => array(
        array('id' => 1, 'rule_id' => 2, 'identifier' => 'route_parameter', 'parameters' => '{"some_param": [1,2]}'),
        array('id' => 2, 'rule_id' => 3, 'identifier' => 'route_parameter', 'parameters' => '{"some_param": [3,4]}'),
        array('id' => 3, 'rule_id' => 3, 'identifier' => 'route_parameter', 'parameters' => '{"some_other_param": [5,6]}'),
    ),
);
