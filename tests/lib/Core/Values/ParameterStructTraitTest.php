<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Core\Stubs\ParameterBasedValue;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use PHPUnit\Framework\TestCase;

final class ParameterStructTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\ParameterStructTrait
     */
    private $struct;

    public function setUp()
    {
        $this->struct = $this->getMockForTrait(ParameterStructTrait::class);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValues
     */
    public function testDefaultProperties()
    {
        $this->assertEquals([], $this->struct->getParameterValues());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValues
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::setParameterValues
     */
    public function testSetParameterValues()
    {
        $this->struct->setParameterValues(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ]
        );

        $this->assertEquals(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::setParameterValue
     */
    public function testSetParameterValue()
    {
        $this->struct->setParameterValue('some_param', 'some_value');
        $this->struct->setParameterValue('some_other_param', 'some_other_value');

        $this->assertEquals(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::setParameterValue
     */
    public function testOverwriteParameterValues()
    {
        $this->struct->setParameterValue('some_param', 'some_value');
        $this->struct->setParameterValue('some_param', 'new_value');

        $this->assertEquals(['some_param' => 'new_value'], $this->struct->getParameterValues());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValue
     */
    public function testGetParameterValue()
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->assertEquals('some_value', $this->struct->getParameterValue('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValue
     * @expectedException \Netgen\BlockManager\Exception\Core\ParameterException
     * @expectedExceptionMessage Parameter value for "some_other_param" parameter does not exist.
     */
    public function testGetParameterValueThrowsParameterException()
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->struct->getParameterValue('some_other_param');
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::hasParameterValue
     */
    public function testHasParameterValue()
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->assertTrue($this->struct->hasParameterValue('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::hasParameterValue
     */
    public function testHasParameterValueWithNoValue()
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->assertFalse($this->struct->hasParameterValue('some_other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fill
     */
    public function testFill()
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fill($parameterDefinitions, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fill
     */
    public function testFillWithMissingValues()
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fill($parameterDefinitions, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillFromValue
     */
    public function testFillFromValue()
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $value = new ParameterBasedValue(
            [
                'parameters' => [
                    'css_class' => new Parameter(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $parameterDefinitions->getParameterDefinition('css_class'),
                        ]
                    ),
                    'inner' => new Parameter(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $parameterDefinitions->getParameterDefinition('compound')->getParameterDefinition('inner'),
                        ]
                    ),
                ],
            ]
        );

        $this->struct->fillFromValue($parameterDefinitions, $value);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillFromHash
     */
    public function testFillFromHash()
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillFromHash($parameterDefinitions, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillFromHash
     */
    public function testFillFromHashWithMissingValues()
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillFromHash($parameterDefinitions, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @return \Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinitionCollection
     */
    private function buildParameterDefinitionCollection()
    {
        $compoundParameter = new CompoundParameterDefinition(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => new ParameterDefinition(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'defaultValue' => 'inner_default',
                        ]
                    ),
                ],
            ]
        );

        $parameterDefinitions = [
            'css_class' => new ParameterDefinition(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css_default',
                ]
            ),
            'css_id' => new ParameterDefinition(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id_default',
                ]
            ),
            'compound' => $compoundParameter,
        ];

        return new ParameterDefinitionCollection($parameterDefinitions);
    }
}
