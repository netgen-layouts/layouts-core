<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer;

class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeRules()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => null,
                'matcher' => null,
                'value_identifier' => null,
                'value' => null,
            ),
        );

        $expected = array(
            1 => array(
                'layout_id' => 2,
                'conditions' => array(),
            ),
        );

        $normalizer = new Normalizer();
        self::assertEquals($expected, $normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeRulesWithCondition()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'matcher' => 'matcher',
                'value_identifier' => 'identifier',
                'value' => 3,
            ),
        );

        $expected = array(
            1 => array(
                'layout_id' => 2,
                'conditions' => array(
                    1 => array(
                        'matcher' => 'matcher',
                        'value_identifier' => 'identifier',
                        'values' => array(3),
                    ),
                ),
            ),
        );

        $normalizer = new Normalizer();
        self::assertEquals($expected, $normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeRulesWithConditionAndMultipleValues()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'matcher' => 'matcher',
                'value_identifier' => 'identifier',
                'value' => 3,
            ),
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'matcher' => 'matcher',
                'value_identifier' => 'identifier',
                'value' => 4,
            ),
        );

        $expected = array(
            1 => array(
                'layout_id' => 2,
                'conditions' => array(
                    1 => array(
                        'matcher' => 'matcher',
                        'value_identifier' => 'identifier',
                        'values' => array(3, 4),
                    ),
                ),
            ),
        );

        $normalizer = new Normalizer();
        self::assertEquals($expected, $normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeRulesWithMultipleConditions()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'matcher' => 'matcher',
                'value_identifier' => 'identifier',
                'value' => 3,
            ),
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 2,
                'matcher' => 'other_matcher',
                'value_identifier' => 'other_identifier',
                'value' => 5,
            ),
        );

        $expected = array(
            1 => array(
                'layout_id' => 2,
                'conditions' => array(
                    1 => array(
                        'matcher' => 'matcher',
                        'value_identifier' => 'identifier',
                        'values' => array(3),
                    ),
                    2 => array(
                        'matcher' => 'other_matcher',
                        'value_identifier' => 'other_identifier',
                        'values' => array(5),
                    ),
                ),
            ),
        );

        $normalizer = new Normalizer();
        self::assertEquals($expected, $normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeRulesWithMultipleConditionsAndMultipleValues()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'matcher' => 'matcher',
                'value_identifier' => 'identifier',
                'value' => 3,
            ),
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'matcher' => 'matcher',
                'value_identifier' => 'identifier',
                'value' => 4,
            ),
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 2,
                'matcher' => 'other_matcher',
                'value_identifier' => 'other_identifier',
                'value' => 5,
            ),
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 2,
                'matcher' => 'other_matcher',
                'value_identifier' => 'other_identifier',
                'value' => 6,
            ),
        );

        $expected = array(
            1 => array(
                'layout_id' => 2,
                'conditions' => array(
                    1 => array(
                        'matcher' => 'matcher',
                        'value_identifier' => 'identifier',
                        'values' => array(3, 4),
                    ),
                    2 => array(
                        'matcher' => 'other_matcher',
                        'value_identifier' => 'other_identifier',
                        'values' => array(5, 6),
                    ),
                ),
            ),
        );

        $normalizer = new Normalizer();
        self::assertEquals($expected, $normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeMultipleRules()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => null,
                'matcher' => null,
                'value_identifier' => null,
                'value' => null,
            ),
            array(
                'id' => 2,
                'layout_id' => 3,
                'condition_id' => null,
                'matcher' => null,
                'value_identifier' => null,
                'value' => null,
            ),
        );

        $expected = array(
            1 => array(
                'layout_id' => 2,
                'conditions' => array(),
            ),
            2 => array(
                'layout_id' => 3,
                'conditions' => array(),
            ),
        );

        $normalizer = new Normalizer();
        self::assertEquals($expected, $normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeMultipleRulesWithConditions()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'matcher' => 'matcher',
                'value_identifier' => 'value_identifier',
                'value' => 3,
            ),
            array(
                'id' => 2,
                'layout_id' => 3,
                'condition_id' => 2,
                'matcher' => 'other_matcher',
                'value_identifier' => 'other_value_identifier',
                'value' => 4,
            ),
        );

        $expected = array(
            1 => array(
                'layout_id' => 2,
                'conditions' => array(
                    1 => array(
                        'matcher' => 'matcher',
                        'value_identifier' => 'value_identifier',
                        'values' => array(3),
                    ),
                ),
            ),
            2 => array(
                'layout_id' => 3,
                'conditions' => array(
                    2 => array(
                        'matcher' => 'other_matcher',
                        'value_identifier' => 'other_value_identifier',
                        'values' => array(4),
                    ),
                ),
            ),
        );

        $normalizer = new Normalizer();
        self::assertEquals($expected, $normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeMultipleRulesWithMultipleConditions()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'matcher' => 'matcher',
                'value_identifier' => 'value_identifier',
                'value' => 3,
            ),
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 2,
                'matcher' => 'matcher2',
                'value_identifier' => 'value_identifier2',
                'value' => 4,
            ),
            array(
                'id' => 2,
                'layout_id' => 3,
                'condition_id' => 3,
                'matcher' => 'other_matcher',
                'value_identifier' => 'other_value_identifier',
                'value' => 5,
            ),
            array(
                'id' => 2,
                'layout_id' => 3,
                'condition_id' => 4,
                'matcher' => 'other_matcher2',
                'value_identifier' => 'other_value_identifier2',
                'value' => 6,
            ),
        );

        $expected = array(
            1 => array(
                'layout_id' => 2,
                'conditions' => array(
                    1 => array(
                        'matcher' => 'matcher',
                        'value_identifier' => 'value_identifier',
                        'values' => array(3),
                    ),
                    2 => array(
                        'matcher' => 'matcher2',
                        'value_identifier' => 'value_identifier2',
                        'values' => array(4),
                    ),
                ),
            ),
            2 => array(
                'layout_id' => 3,
                'conditions' => array(
                    3 => array(
                        'matcher' => 'other_matcher',
                        'value_identifier' => 'other_value_identifier',
                        'values' => array(5),
                    ),
                    4 => array(
                        'matcher' => 'other_matcher2',
                        'value_identifier' => 'other_value_identifier2',
                        'values' => array(6),
                    ),
                ),
            ),
        );

        $normalizer = new Normalizer();
        self::assertEquals($expected, $normalizer->normalizeRules($data));
    }
}
