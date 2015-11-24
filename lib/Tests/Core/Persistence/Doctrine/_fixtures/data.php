<?php

return array(
    'ngbm_layout' => array(
        array('id' => 1, 'parent_id' => null, 'identifier' => '3_zones_a', 'created' => 1447065813, 'modified' => 1447065813),
        array('id' => 2, 'parent_id' => null, 'identifier' => '3_zones_b', 'created' => 1447065813, 'modified' => 1447065813),
    ),
    'ngbm_zone' => array(
        array('id' => 1, 'layout_id' => 1, 'identifier' => 'top_left'),
        array('id' => 2, 'layout_id' => 1, 'identifier' => 'top_right'),
        array('id' => 3, 'layout_id' => 1, 'identifier' => 'bottom'),
        array('id' => 4, 'layout_id' => 2, 'identifier' => 'top'),
        array('id' => 5, 'layout_id' => 2, 'identifier' => 'bottom_left'),
        array('id' => 6, 'layout_id' => 2, 'identifier' => 'bottom_right'),
    ),
    'ngbm_block' => array(
        array('id' => 1, 'zone_id' => 2, 'definition_identifier' => 'paragraph', 'view_type' => 'default', 'name' => 'My block', 'parameters' => '{"some_param": "some_value"}'),
        array('id' => 2, 'zone_id' => 2, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My other block', 'parameters' => '{"other_param": "other_value"}'),
        array('id' => 3, 'zone_id' => 6, 'definition_identifier' => 'paragraph', 'view_type' => 'large', 'name' => 'My third block', 'parameters' => '{"test_param": "test_value"}'),
        array('id' => 4, 'zone_id' => 6, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My fourth block', 'parameters' => '{"the_answer": 42}'),
    ),
);
