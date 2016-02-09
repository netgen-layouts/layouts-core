<?php

return array(
    'ngbm_layout' => array(
        array('id' => 1, 'parent_id' => null, 'identifier' => '3_zones_a', 'name' => 'My layout', 'created' => 1447065813, 'modified' => 1447065813, 'status' => 1),
        array('id' => 1, 'parent_id' => null, 'identifier' => '3_zones_a', 'name' => 'My layout', 'created' => 1447065813, 'modified' => 1447065813, 'status' => 0),
        array('id' => 2, 'parent_id' => null, 'identifier' => '3_zones_b', 'name' => 'My other layout', 'created' => 1447065813, 'modified' => 1447065813, 'status' => 1),
        array('id' => 2, 'parent_id' => null, 'identifier' => '3_zones_b', 'name' => 'My other layout', 'created' => 1447065813, 'modified' => 1447065813, 'status' => 0),
    ),
    'ngbm_zone' => array(
        array('identifier' => 'top_left', 'layout_id' => 1, 'status' => 1),
        array('identifier' => 'top_right', 'layout_id' => 1, 'status' => 1),
        array('identifier' => 'bottom', 'layout_id' => 1, 'status' => 1),
        array('identifier' => 'top_left', 'layout_id' => 1, 'status' => 0),
        array('identifier' => 'top_right', 'layout_id' => 1, 'status' => 0),
        array('identifier' => 'bottom', 'layout_id' => 1, 'status' => 0),
        array('identifier' => 'top', 'layout_id' => 2, 'status' => 1),
        array('identifier' => 'bottom_left', 'layout_id' => 2, 'status' => 1),
        array('identifier' => 'bottom_right', 'layout_id' => 2, 'status' => 1),
        array('identifier' => 'top', 'layout_id' => 2, 'status' => 0),
        array('identifier' => 'bottom_left', 'layout_id' => 2, 'status' => 0),
        array('identifier' => 'bottom_right', 'layout_id' => 2, 'status' => 0),
    ),
    'ngbm_block' => array(
        array('id' => 1, 'layout_id' => 1, 'zone_identifier' => 'top_right', 'position' => 0, 'definition_identifier' => 'paragraph', 'view_type' => 'default', 'name' => 'My block', 'parameters' => '{"some_param": "some_value"}', 'status' => 1),
        array('id' => 2, 'layout_id' => 1, 'zone_identifier' => 'top_right', 'position' => 1, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My other block', 'parameters' => '{"other_param": "other_value"}', 'status' => 1),
        array('id' => 1, 'layout_id' => 1, 'zone_identifier' => 'top_right', 'position' => 0, 'definition_identifier' => 'paragraph', 'view_type' => 'default', 'name' => 'My block', 'parameters' => '{"some_param": "some_value"}', 'status' => 0),
        array('id' => 2, 'layout_id' => 1, 'zone_identifier' => 'top_right', 'position' => 1, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My other block', 'parameters' => '{"other_param": "other_value"}', 'status' => 0),
        array('id' => 3, 'layout_id' => 2, 'zone_identifier' => 'bottom_right', 'position' => 0, 'definition_identifier' => 'paragraph', 'view_type' => 'large', 'name' => 'My third block', 'parameters' => '{"test_param": "test_value"}', 'status' => 1),
        array('id' => 4, 'layout_id' => 2, 'zone_identifier' => 'bottom_right', 'position' => 1, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My fourth block', 'parameters' => '{"the_answer": 42}', 'status' => 1),
        array('id' => 3, 'layout_id' => 2, 'zone_identifier' => 'bottom_right', 'position' => 0, 'definition_identifier' => 'paragraph', 'view_type' => 'large', 'name' => 'My third block', 'parameters' => '{"test_param": "test_value"}', 'status' => 0),
        array('id' => 4, 'layout_id' => 2, 'zone_identifier' => 'bottom_right', 'position' => 1, 'definition_identifier' => 'title', 'view_type' => 'small', 'name' => 'My fourth block', 'parameters' => '{"the_answer": 42}', 'status' => 0),
    ),
);
