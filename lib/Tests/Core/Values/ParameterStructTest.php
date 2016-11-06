<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use PHPUnit\Framework\TestCase;

class ParameterStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\ParameterStruct
     */
    protected $struct;

    public function setUp()
    {
        $this->struct = $this->getMockForAbstractClass(ParameterStruct::class);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::__construct
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::getParameters
     */
    public function testDefaultProperties()
    {
        $this->assertEquals(array(), $this->struct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::setParameters
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::getParameters
     */
    public function testSetParameters()
    {
        $this->struct->setParameters(
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
            $this->struct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::setParameter
     */
    public function testSetParameter()
    {
        $this->struct->setParameter('some_param', 'some_value');
        $this->struct->setParameter('some_other_param', 'some_other_value');

        $this->assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $this->struct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::setParameter
     */
    public function testOverwriteParameters()
    {
        $this->struct->setParameter('some_param', 'some_value');
        $this->struct->setParameter('some_param', 'new_value');

        $this->assertEquals(array('some_param' => 'new_value'), $this->struct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::getParameter
     */
    public function testGetParameter()
    {
        $this->struct->setParameter('some_param', 'some_value');

        $this->assertEquals('some_value', $this->struct->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::getParameter
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetParameterThrowsInvalidArgumentException()
    {
        $this->struct->setParameter('some_param', 'some_value');

        $this->struct->getParameter('some_other_param');
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::hasParameter
     */
    public function testHasParameter()
    {
        $this->struct->setParameter('some_param', 'some_value');

        $this->assertTrue($this->struct->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::hasParameter
     */
    public function testHasParameterWithNoParameter()
    {
        $this->struct->setParameter('some_param', 'some_value');

        $this->assertFalse($this->struct->hasParameter('some_other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::fillValues
     */
    public function testFillValues()
    {
        $compoundParameter = new CompoundParameter(
            'compound',
            new ParameterType\Compound\BooleanType(),
            array(),
            false,
            true
        );

        $compoundParameter->setParameters(
            array(
                'inner' => new Parameter('inner', new ParameterType\TextLineType(), array(), false, 'inner_default'),
            )
        );

        $parameters = array(
            'css_class' => new Parameter('css_class', new ParameterType\TextLineType(), array(), false, 'css'),
            'css_id' => new Parameter('css_id', new ParameterType\TextLineType(), array(), false, 'id'),
            'compound' => $compoundParameter,
        );

        $initialValues = array(
            'css_class' => 'initial_css',
            'inner' => 'inner_initial',
        );

        $this->struct->fillValues($parameters, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'initial_css',
                'css_id' => 'id',
                'compound' => true,
                'inner' => 'inner_initial',
            ),
            $this->struct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStruct::fillValues
     */
    public function testFillValuesWithoutDefaults()
    {
        $compoundParameter = new CompoundParameter(
            'compound',
            new ParameterType\Compound\BooleanType(),
            array(),
            false,
            true
        );

        $compoundParameter->setParameters(
            array(
                'inner' => new Parameter('inner', new ParameterType\TextLineType(), array(), false, 'inner_default'),
            )
        );

        $parameters = array(
            'css_class' => new Parameter('css_class', new ParameterType\TextLineType(), array(), false, 'css'),
            'css_id' => new Parameter('css_id', new ParameterType\TextLineType(), array(), false, 'id'),
            'compound' => $compoundParameter,
        );

        $initialValues = array(
            'css_class' => 'initial_css',
            'inner' => 'inner_initial',
        );

        $this->struct->fillValues($parameters, $initialValues, false);

        $this->assertEquals(
            array(
                'css_class' => 'initial_css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner_initial',
            ),
            $this->struct->getParameters()
        );
    }
}
