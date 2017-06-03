<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use PHPUnit\Framework\TestCase;

class ParameterStructTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\ParameterStructTrait
     */
    protected $struct;

    public function setUp()
    {
        $this->struct = $this->getMockForTrait(ParameterStructTrait::class);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValues
     */
    public function testDefaultProperties()
    {
        $this->assertEquals(array(), $this->struct->getParameterValues());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::setParameterValues
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValues
     */
    public function testSetParameterValues()
    {
        $this->struct->setParameterValues(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            )
        );

        $this->assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
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
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
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

        $this->assertEquals(array('some_param' => 'new_value'), $this->struct->getParameterValues());
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
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillValues
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::buildValue
     */
    public function testFillValues()
    {
        $parameterCollection = $this->buildParameterCollection();

        $initialValues = array(
            'css_class' => 'initial_css',
            'inner' => 'inner_initial',
        );

        $this->struct->fillValues($parameterCollection, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'initial_css',
                'css_id' => 'id',
                'compound' => true,
                'inner' => 'inner_initial',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillValues
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::buildValue
     */
    public function testFillValuesWithParameterValueInstances()
    {
        $parameterCollection = $this->buildParameterCollection();

        $initialValues = array(
            'css_class' => new ParameterValue(
                array(
                    'value' => 'initial_css',
                )
            ),
            'inner' => new ParameterValue(
                array(
                    'value' => 'inner_initial',
                )
            ),
        );

        $this->struct->fillValues($parameterCollection, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'initial_css',
                'css_id' => 'id',
                'compound' => true,
                'inner' => 'inner_initial',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillValues
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::buildValue
     */
    public function testFillValuesWithoutDefaults()
    {
        $parameterCollection = $this->buildParameterCollection();

        $initialValues = array(
            'css_class' => 'initial_css',
            'inner' => 'inner_initial',
        );

        $this->struct->fillValues($parameterCollection, $initialValues, false);

        $this->assertEquals(
            array(
                'css_class' => 'initial_css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner_initial',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @return \Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection
     */
    protected function buildParameterCollection()
    {
        $compoundParameter = new CompoundParameter(
            array(
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
            )
        );

        $compoundParameter->setParameters(
            array(
                'inner' => new Parameter(
                    array(
                        'name' => 'inner',
                        'type' => new ParameterType\TextLineType(),
                        'defaultValue' => 'inner_default',
                    )
                ),
            )
        );

        $parameters = array(
            'css_class' => new Parameter(
                array(
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css',
                )
            ),
            'css_id' => new Parameter(
                array(
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id',
                )
            ),
            'compound' => $compoundParameter,
        );

        return new ParameterCollection($parameters);
    }
}
