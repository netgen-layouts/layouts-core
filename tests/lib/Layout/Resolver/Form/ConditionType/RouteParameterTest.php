<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Form\KeyValuesType;
use Netgen\Layouts\Layout\Resolver\ConditionType\RouteParameter;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\RouteParameter as RouteParameterMapper;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;

final class RouteParameterTest extends FormTestCase
{
    private RouteParameter $conditionType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conditionType = new RouteParameter();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType::buildForm
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType::buildView
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper::getFormOptions
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper::handleForm
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\RouteParameter::getFormOptions
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\RouteParameter::getFormType
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'value' => [
                'parameter_name' => 'some_name',
                'parameter_values' => ['value1', 'value1'],
            ],
        ];

        $struct = new ConditionCreateStruct();

        $form = $this->factory->create(
            ConditionType::class,
            $struct,
            ['condition_type' => $this->conditionType],
        );

        $valueFormConfig = $form->get('value')->getConfig();
        self::assertInstanceOf(KeyValuesType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        self::assertTrue($form->isSynchronized());

        self::assertSame(
            [
                'parameter_name' => 'some_name',
                'parameter_values' => ['value1', 'value1'],
            ],
            $struct->value,
        );

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
                    'route_parameter' => new RouteParameterMapper(),
                ],
            ),
        );
    }
}
