<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Form\KeyValuesType;
use Netgen\BlockManager\Layout\Resolver\ConditionType\QueryParameter;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\QueryParameter as QueryParameterMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;

class QueryParameterTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    protected $conditionType;

    public function setUp()
    {
        parent::setUp();

        $this->conditionType = new QueryParameter();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new ConditionType(
            array(
                'query_parameter' => new QueryParameterMapper(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::mapOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\QueryParameter::getFormType
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\QueryParameter::mapOptions
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
