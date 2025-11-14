<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Exception\Layout\ConditionTypeException;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType as ConditionTypeForm;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionTypeMapper;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[CoversClass(ConditionTypeForm::class)]
final class ConditionTypeTest extends FormTestCase
{
    private ConditionType1 $conditionType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conditionType = new ConditionType1();
    }

    public function testBuildFormThrowsConditionTypeExceptionWithNoMapper(): void
    {
        $this->expectException(ConditionTypeException::class);
        $this->expectExceptionMessage('Form mapper for "condition1" condition type does not exist.');

        $this->factory->create(
            ConditionTypeForm::class,
            new ConditionCreateStruct(),
            ['condition_type' => $this->conditionType],
        );
    }

    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new ConditionCreateStruct();

        $options = $optionsResolver->resolve(
            [
                'condition_type' => $this->conditionType,
                'data' => $struct,
            ],
        );

        self::assertSame($this->conditionType, $options['condition_type']);
        self::assertSame($struct, $options['data']);
    }

    public function testConfigureOptionsWithMissingConditionType(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "condition_type" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    public function testConfigureOptionsWithInvalidConditionType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "condition_type" with value "" is expected to be of type "Netgen\Layouts\Layout\Resolver\ConditionTypeInterface", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'condition_type' => '',
            ],
        );
    }

    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\LayoutResolver\ConditionStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'condition_type' => $this->conditionType,
                'data' => '',
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new ConditionTypeForm(new Container(['other_type' => new ConditionTypeMapper()]));
    }
}
