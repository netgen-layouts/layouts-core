<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType as ConditionTypeForm;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionTypeMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConditionTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    public function setUp(): void
    {
        parent::setUp();

        $this->conditionType = new ConditionType1();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     */
    public function testBuildFormThrowsConditionTypeException(): void
    {
        $this->expectException(ConditionTypeException::class);
        $this->expectExceptionMessage('Form mapper for "condition1" condition type does not exist.');

        $this->factory->create(
            ConditionTypeForm::class,
            new ConditionCreateStruct(),
            ['condition_type' => $this->conditionType]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new ConditionCreateStruct();

        $options = $optionsResolver->resolve(
            [
                'condition_type' => $this->conditionType,
                'data' => $struct,
            ]
        );

        self::assertSame($this->conditionType, $options['condition_type']);
        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     */
    public function testConfigureOptionsWithMissingConditionType(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "condition_type" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     */
    public function testConfigureOptionsWithInvalidConditionType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "condition_type" with value "" is expected to be of type "Netgen\\BlockManager\\Layout\\Resolver\\ConditionTypeInterface", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'condition_type' => '',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\\BlockManager\\API\\Values\\LayoutResolver\\ConditionStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'condition_type' => $this->conditionType,
                'data' => '',
            ]
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new ConditionTypeForm(['other_type' => new ConditionTypeMapper()]);
    }
}
