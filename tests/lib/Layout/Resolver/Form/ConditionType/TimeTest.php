<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Layout\Resolver\ConditionType\Time;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\Time as TimeMapper;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Type\TimeType;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\FormTypeInterface;

#[CoversClass(ConditionType::class)]
#[CoversClass(Mapper::class)]
#[CoversClass(TimeMapper::class)]
final class TimeTest extends FormTestCase
{
    private Time $conditionType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conditionType = new Time();
    }

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
            ['condition_type' => $this->conditionType],
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
                    'time' => new TimeMapper(),
                ],
            ),
        );
    }
}
