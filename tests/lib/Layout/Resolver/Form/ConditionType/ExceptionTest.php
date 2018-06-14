<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Layout\Resolver\ConditionType\Exception;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception as ExceptionMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;

final class ExceptionTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    public function setUp(): void
    {
        parent::setUp();

        $this->conditionType = new Exception();
    }

    public function getMainType(): FormTypeInterface
    {
        return new ConditionType(
            [
                'exception' => new ExceptionMapper(),
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildView
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception::getFormType
     */
    public function testSubmitValidData(): void
    {
        $submittedData = ['value' => [404]];

        $updatedStruct = new ConditionCreateStruct();
        $updatedStruct->value = [404];

        $form = $this->factory->create(
            ConditionType::class,
            new ConditionCreateStruct(),
            ['condition_type' => $this->conditionType]
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(ChoiceType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $formView = $form->createView();
        $this->assertArrayHasKey('value', $formView->children);

        $this->assertArrayHasKey('condition_type', $formView->vars);
        $this->assertEquals($this->conditionType, $formView->vars['condition_type']);
    }
}
