<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler\Compound;

use Netgen\BlockManager\Parameters\Form\CompoundBooleanType;
use Netgen\BlockManager\Parameters\Parameter\Compound\Boolean as BooleanParameter;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Boolean();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(CompoundBooleanType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean::getDefaultOptions
     */
    public function testGetDefaultOptions()
    {
        $this->assertEquals(
            array(
                'label' => false,
                'required' => true,
                'property_path' => 'parameters[name]',
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'checkbox_required' => true,
                'checkbox_label' => 'label.name',
                'checkbox_property_path' => 'parameters[name]',
            ),
            $this->parameterHandler->getDefaultOptions(
                new BooleanParameter(array(), array('reverse' => true), true),
                'name',
                array(
                    'label_prefix' => 'label',
                    'property_path_prefix' => 'parameters',
                )
            )
        );
    }
}
