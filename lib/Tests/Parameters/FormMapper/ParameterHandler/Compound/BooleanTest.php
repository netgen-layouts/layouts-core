<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler\Compound;

use Netgen\BlockManager\Parameters\Form\CompoundBooleanType;
use Netgen\BlockManager\Parameters\Parameter\Compound\Boolean as BooleanParameter;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Validator\Constraints;
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
                'parameter_validation_groups' => array('group'),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'checkbox_required' => true,
                'checkbox_label' => 'label.name',
                'checkbox_property_path' => 'parameters[name]',
                'checkbox_constraints' => array(
                    new Constraints\NotNull(),
                    new Constraints\Type(array('type' => 'bool')),
                ),
            ),
            $this->parameterHandler->getDefaultOptions(
                new BooleanParameter(array(), array(), true),
                'name',
                array(
                    'parameter_validation_groups' => array('group'),
                    'label_prefix' => 'label',
                    'property_path_prefix' => 'parameters',
                )
            )
        );
    }
}
