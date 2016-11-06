<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\Form\KeyValuesType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\RouteParameter as RouteParameterMapper;
use Netgen\BlockManager\Layout\Resolver\ConditionType\RouteParameter;
use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;

class RouteParameterTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    protected $conditionType;

    public function setUp()
    {
        parent::setUp();

        $this->conditionType = new RouteParameter();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new ConditionType(
            array(
                'route_parameter' => new RouteParameterMapper(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::mapOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\RouteParameter::getFormType
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\RouteParameter::mapOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'value' => array(
                'parameter_name' => 'some_name',
                'parameter_values' => array('value1', 'value1'),
            ),
        );

        $updatedStruct = new ConditionCreateStruct();
        $updatedStruct->value = array(
            'parameter_name' => 'some_name',
            'parameter_values' => array('value1', 'value1'),
        );

        $form = $this->factory->create(
            ConditionType::class,
            new ConditionCreateStruct(),
            array('conditionType' => $this->conditionType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(KeyValuesType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $this->assertArrayHasKey('value', $form->createView()->children);
    }
}
