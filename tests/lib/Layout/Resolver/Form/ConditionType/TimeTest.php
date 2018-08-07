<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Layout\Resolver\ConditionType\Time;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Time as TimeMapper;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Type\TimeType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;

final class TimeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    public function setUp(): void
    {
        parent::setUp();

        $this->conditionType = new Time();
    }

    public function getMainType(): FormTypeInterface
    {
        return new ConditionType(
            [
                'time' => new TimeMapper(),
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildView
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Time::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Time::getFormType
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'value' => [
                'from' => [
                    'datetime' => '2018-02-15T13:00',
                    'timezone' => 'Antarctica/Casey',
                ],
                'to' => [
                    'datetime' => '2018-02-20T13:00',
                    'timezone' => 'Antarctica/Casey',
                ],
            ],
        ];

        $struct = new ConditionCreateStruct();

        $form = $this->factory->create(
            ConditionType::class,
            $struct,
            ['condition_type' => $this->conditionType]
        );

        $valueFormConfig = $form->get('value')->getConfig();
        self::assertInstanceOf(TimeType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        self::assertTrue($form->isSynchronized());

        self::assertSame(
            [
                'from' => [
                    'datetime' => '2018-02-15 13:00:00',
                    'timezone' => 'Antarctica/Casey',
                ],
                'to' => [
                    'datetime' => '2018-02-20 13:00:00',
                    'timezone' => 'Antarctica/Casey',
                ],
            ],
            $struct->value
        );

        $formView = $form->createView();

        self::assertArrayHasKey('value', $formView->children);

        self::assertArrayHasKey('condition_type', $formView->vars);
        self::assertSame($this->conditionType, $formView->vars['condition_type']);
    }
}
