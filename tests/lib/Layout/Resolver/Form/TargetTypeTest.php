<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Exception\Layout\TargetTypeException;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType as TargetTypeForm;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetTypeMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TargetTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    private $targetType;

    public function setUp(): void
    {
        parent::setUp();

        $this->targetType = new TargetType1(42);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     */
    public function testBuildFormThrowsTargetTypeException(): void
    {
        $this->expectException(TargetTypeException::class);
        $this->expectExceptionMessage('Form mapper for "target1" target type does not exist.');

        $this->factory->create(
            TargetTypeForm::class,
            new TargetCreateStruct(),
            ['target_type' => $this->targetType]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new TargetCreateStruct();

        $options = $optionsResolver->resolve(
            [
                'target_type' => $this->targetType,
                'data' => $struct,
            ]
        );

        self::assertSame($this->targetType, $options['target_type']);
        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     */
    public function testConfigureOptionsWithMissingTargetType(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "target_type" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     */
    public function testConfigureOptionsWithInvalidTargetType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "target_type" with value "" is expected to be of type "Netgen\\BlockManager\\Layout\\Resolver\\TargetTypeInterface", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'target_type' => '',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\\BlockManager\\API\\Values\\LayoutResolver\\TargetStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'target_type' => $this->targetType,
                'data' => '',
            ]
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new TargetTypeForm(['other_type' => new TargetTypeMapper()]);
    }
}
