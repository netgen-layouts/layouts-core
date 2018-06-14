<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType as ConditionTypeForm;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionTypeMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
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

        $this->conditionType = new ConditionType('type');
    }

    public function getMainType(): FormTypeInterface
    {
        return new ConditionTypeForm(['other_type' => new ConditionTypeMapper()]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     * @expectedException \Netgen\BlockManager\Exception\Layout\ConditionTypeException
     * @expectedExceptionMessage Form mapper for "type" condition type does not exist.
     */
    public function testBuildFormThrowsConditionTypeException(): void
    {
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

        $options = $optionsResolver->resolve(
            [
                'condition_type' => $this->conditionType,
                'data' => new ConditionCreateStruct(),
            ]
        );

        $this->assertEquals($this->conditionType, $options['condition_type']);
        $this->assertEquals(new ConditionCreateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "condition_type" is missing.
     */
    public function testConfigureOptionsWithMissingConditionType(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "condition_type" with value "" is expected to be of type "Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidConditionType(): void
    {
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
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\LayoutResolver\ConditionStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
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
}
