<?php

return array(
    'ngbm_layout' => array(
        array('id' => 1, 'parent_id' => null, 'identifier' => '3_zones_a', 'name' => 'My layout', 'created' => 1447065813, 'modified' => 1447065813, 'status' => 1),
        array('id' => 1, 'parent_id' => null, 'identifier' => '3_zones_a', 'name' => 'My layout', 'created' => 1447065813, 'modified' => 1447065813, 'status' => 0),
        array('id' => 2, 'parent_id' => null, 'identifier' => '3_zones_b', 'name' => 'My other layout', 'created' => 1447065813, 'modified' => 1447065813, 'status' => 1),
        array('id' => 2, 'parent_id' => null, 'identifier' => '3_zones_b', 'name' => 'My other layout', 'created' => 1447065813, 'modified' => 1447065813, 'status' => 0),
    ),
    'ngbm_zone' => array(
        array('id' => 1, 'layout_id' => 1, 'identifier' => 'top_left', 'status' => 1),
        array('id' => 2, 'layout_id' => 1, 'identifier' => 'top_right', 'status' => 1),
        array('id' => 3, 'layout_id' => 1, 'identifier' => 'bottom', 'status' => 1),
        array('id' => 1, 'layout_id' => 1, 'identifier' => 'top_left', 'status' => 0),
        array('id' => 2, 'layout_id' => 1, 'identifier' => 'top_right', 'status' => 0),
        array('id' => 3, 'layout_id' => 1, 'identifier' => 'bottom', 'status' => 0),
        array('id' => 4, 'layout_id' => 2, 'identifier' => 'top', 'status' => 1),
        array('id' => 5, 'layout_id' => 2, 'identifier' => 'bottom_left', 'status' => 1),
        array('id' => 6, 'layout_id' => 2, 'identifier' => 'bottom_right', 'status' => 1),
        array('id' => 4, 'layout_id' => 2, 'identifier' => 'top', 'status' => 0),
        array('id' => 5, 'layout_id' => 2, 'identifier' => 'bottom_left', 'status' => 0),
        array('id' => 6, 'layout_id' => 2, 'identifier' => 'bottom_right', 'status' => 0),
    ),
    'ngbm_block' => array(
        array('id' => 1, 'zone_id' => 2, 'definition_identifier' => 'paragraph', 'view_type' => 'default', 'name' => 'My block', 'parameters' => '{"some_param": "some_value"}', 'status' => 1),
        array('id' => 2, 'zone_id' => 2, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My other block', 'parameters' => '{"other_param": "other_value"}', 'status' => 1),
        array('id' => 1, 'zone_id' => 2, 'definition_identifier' => 'paragraph', 'view_type' => 'default', 'name' => 'My block', 'parameters' => '{"some_param": "some_value"}', 'status' => 0),
        array('id' => 2, 'zone_id' => 2, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My other block', 'parameters' => '{"other_param": "other_value"}', 'status' => 0),
        array('id' => 3, 'zone_id' => 6, 'definition_identifier' => 'paragraph', 'view_type' => 'large', 'name' => 'My third block', 'parameters' => '{"test_param": "test_value"}', 'status' => 1),
        array('id' => 4, 'zone_id' => 6, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My fourth block', 'parameters' => '{"the_answer": 42}', 'status' => 1),
        array('id' => 3, 'zone_id' => 6, 'definition_identifier' => 'paragraph', 'view_type' => 'large', 'name' => 'My third block', 'parameters' => '{"test_param": "test_value"}', 'status' => 0),
        array('id' => 4, 'zone_id' => 6, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My fourth block', 'parameters' => '{"the_answer": 42}', 'status' => 0),
    ),
);
