<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType\Type;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Type\TimeType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;

use function array_keys;

final class TimeTypeTest extends FormTestCase
{
    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Type\TimeType::buildForm
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Type\TimeType::configureOptions
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'from' => [
                'datetime' => '2018-03-31T01:00',
                'timezone' => 'Antarctica/Casey',
            ],
            'to' => [
                'datetime' => '2018-03-31T02:00',
                'timezone' => 'Antarctica/Casey',
            ],
        ];

        $processedData = [
            'from' => [
                'datetime' => '2018-03-31 01:00:00',
                'timezone' => 'Antarctica/Casey',
            ],
            'to' => [
                'datetime' => '2018-03-31 02:00:00',
                'timezone' => 'Antarctica/Casey',
            ],
        ];

        $form = $this->factory->create(TimeType::class);

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($processedData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Type\TimeType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        self::assertSame('nglayouts_condition_type_time', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        return new TimeType();
    }
}
