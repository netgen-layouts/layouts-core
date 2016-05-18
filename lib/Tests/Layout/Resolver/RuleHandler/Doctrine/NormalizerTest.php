<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\RuleHandler\Doctrine;

use Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer;

class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = new Normalizer();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeRules()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => null,
                'identifier' => null,
                'parameters' => null,
            ),
        );

        $expected = array(
            array(
                'layout_id' => 2,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $this->normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeRulesWithCondition()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'identifier' => 'identifier',
                'parameters' => '[3]',
            ),
        );

        $expected = array(
            array(
                'layout_id' => 2,
                'conditions' => array(
                    array(
                        'identifier' => 'identifier',
                        'parameters' => array(3),
                    ),
                ),
            ),
        );

        self::assertEquals($expected, $this->normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeRulesWithConditionAndEmptyValue()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'identifier' => 'identifier',
                'parameters' => null,
            ),
        );

        $expected = array(
            array(
                'layout_id' => 2,
                'conditions' => array(
                    array(
                        'identifier' => 'identifier',
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        self::assertEquals($expected, $this->normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeRulesWithMultipleConditions()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'identifier' => 'identifier',
                'parameters' => '[3]',
            ),
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 2,
                'identifier' => 'other_matcher',
                'parameters' => '[5]',
            ),
        );

        $expected = array(
            array(
                'layout_id' => 2,
                'conditions' => array(
                    array(
                        'identifier' => 'identifier',
                        'parameters' => array(3),
                    ),
                    array(
                        'identifier' => 'other_matcher',
                        'parameters' => array(5),
                    ),
                ),
            ),
        );

        self::assertEquals($expected, $this->normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeMultipleRules()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => null,
                'identifier' => null,
                'parameters' => null,
            ),
            array(
                'id' => 2,
                'layout_id' => 3,
                'condition_id' => null,
                'identifier' => null,
                'parameters' => null,
            ),
        );

        $expected = array(
            array(
                'layout_id' => 2,
                'conditions' => array(),
            ),
            array(
                'layout_id' => 3,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $this->normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeMultipleRulesWithConditions()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'identifier' => 'identifier',
                'parameters' => '[3]',
            ),
            array(
                'id' => 2,
                'layout_id' => 3,
                'condition_id' => 2,
                'identifier' => 'other_matcher',
                'parameters' => '[4]',
            ),
        );

        $expected = array(
            array(
                'layout_id' => 2,
                'conditions' => array(
                    array(
                        'identifier' => 'identifier',
                        'parameters' => array(3),
                    ),
                ),
            ),
            array(
                'layout_id' => 3,
                'conditions' => array(
                    array(
                        'identifier' => 'other_matcher',
                        'parameters' => array(4),
                    ),
                ),
            ),
        );

        self::assertEquals($expected, $this->normalizer->normalizeRules($data));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\Normalizer::normalizeRules
     */
    public function testNormalizeMultipleRulesWithMultipleConditions()
    {
        $data = array(
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 1,
                'identifier' => 'identifier',
                'parameters' => '[3]',
            ),
            array(
                'id' => 1,
                'layout_id' => 2,
                'condition_id' => 2,
                'identifier' => 'matcher2',
                'parameters' => '[4]',
            ),
            array(
                'id' => 2,
                'layout_id' => 3,
                'condition_id' => 3,
                'identifier' => 'other_matcher',
                'parameters' => '[5]',
            ),
            array(
                'id' => 2,
                'layout_id' => 3,
                'condition_id' => 4,
                'identifier' => 'other_matcher2',
                'parameters' => '[6]',
            ),
        );

        $expected = array(
            array(
                'layout_id' => 2,
                'conditions' => array(
                    array(
                        'identifier' => 'identifier',
                        'parameters' => array(3),
                    ),
                    array(
                        'identifier' => 'matcher2',
                        'parameters' => array(4),
                    ),
                ),
            ),
            array(
                'layout_id' => 3,
                'conditions' => array(
                    array(
                        'identifier' => 'other_matcher',
                        'parameters' => array(5),
                    ),
                    array(
                        'identifier' => 'other_matcher2',
                        'parameters' => array(6),
                    ),
                ),
            ),
        );

        self::assertEquals($expected, $this->normalizer->normalizeRules($data));
    }
}
