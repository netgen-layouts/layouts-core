<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Layout\Resolver\ConditionType\Exception;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\Exception as ExceptionMapper;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;

final class ExceptionTest extends FormTestCase
{
    private Exception $conditionType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conditionType = new Exception();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType::buildForm
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType::buildView
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper::getFormOptions
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper::handleForm
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\Exception::getFormOptions
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\Exception::getFormType
     */
    public function testSubmitValidData(): void
    {
        $submittedData = ['value' => [404]];

        $struct = new ConditionCreateStruct();

        $form = $this->factory->create(
            ConditionType::class,
            $struct,
            ['condition_type' => $this->conditionType],
        );

        $valueFormConfig = $form->get('value')->getConfig();
        self::assertInstanceOf(ChoiceType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        self::assertTrue($form->isSynchronized());

        self::assertSame([404], $struct->value);

        $formView = $form->createView();
        self::assertArrayHasKey('value', $formView->children);

        self::assertArrayHasKey('condition_type', $formView->vars);
        self::assertSame($this->conditionType, $formView->vars['condition_type']);
    }

    protected function getMainType(): FormTypeInterface
    {
        return new ConditionType(
            new Container(
                [
                    'exception' => new ExceptionMapper(),
                ],
            ),
        );
    }
}
