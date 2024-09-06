<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form;

use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Exception\Layout\TargetTypeException;
use Netgen\Layouts\Layout\Resolver\Form\TargetType as TargetTypeForm;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetTypeMapper;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TargetTypeTest extends FormTestCase
{
    private TargetType1 $targetType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetType = new TargetType1(42);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType::__construct
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType::getMapper
     */
    public function testBuildFormThrowsTargetTypeExceptionWithNoMapper(): void
    {
        $this->expectException(TargetTypeException::class);
        $this->expectExceptionMessage('Form mapper for "target1" target type does not exist.');

        $this->factory->create(
            TargetTypeForm::class,
            new TargetCreateStruct(),
            ['target_type' => $this->targetType],
        );
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType::configureOptions
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
            ],
        );

        self::assertSame($this->targetType, $options['target_type']);
        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType::configureOptions
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
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType::configureOptions
     */
    public function testConfigureOptionsWithInvalidTargetType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "target_type" with value "" is expected to be of type "Netgen\Layouts\Layout\Resolver\TargetTypeInterface", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'target_type' => '',
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\LayoutResolver\TargetStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'target_type' => $this->targetType,
                'data' => '',
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new TargetTypeForm(new Container(['other_type' => new TargetTypeMapper()]));
    }
}
