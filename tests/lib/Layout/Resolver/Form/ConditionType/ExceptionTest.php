<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Layout\Resolver\ConditionType\Exception;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception as ExceptionMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ExceptionTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    public function setUp()
    {
        parent::setUp();

        $this->conditionType = new Exception();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new ConditionType(
            array(
                'exception' => new ExceptionMapper(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception::getFormType
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception::getFormOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array('value' => array(404));

        $updatedStruct = new ConditionCreateStruct();
        $updatedStruct->value = array(404);

        $form = $this->factory->create(
            ConditionType::class,
            new ConditionCreateStruct(),
            array('conditionType' => $this->conditionType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(ChoiceType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $this->assertArrayHasKey('value', $form->createView()->children);
    }
}
