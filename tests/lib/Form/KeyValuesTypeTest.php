<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Form;

use Netgen\Layouts\Form\KeyValuesType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class KeyValuesTypeTest extends FormTestCase
{
    /**
     * @covers \Netgen\Layouts\Form\KeyValuesType::buildForm
     * @covers \Netgen\Layouts\Form\KeyValuesType::buildView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'some_key' => 'key_value',
            'some_value' => ['value1', 'value2'],
        ];

        $form = $this->factory->create(
            KeyValuesType::class,
            null,
            [
                'key_name' => 'some_key',
                'key_label' => 'Key',
                'values_name' => 'some_value',
                'values_label' => 'Value',
                'values_type' => TextType::class,
                'values_constraints' => [
                    new Constraints\NotBlank(),
                ],
            ],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($submittedData, $form->getData());

        self::assertSame('Key', $form->get('some_key')->getConfig()->getOption('label'));
        self::assertSame('Value', $form->get('some_value')->getConfig()->getOption('label'));

        self::assertInstanceOf(
            CollectionType::class,
            $form->get('some_value')->getConfig()->getType()->getInnerType(),
        );

        $view = $form->createView();

        self::assertArrayHasKey('key_name', $view->vars);
        self::assertArrayHasKey('values_name', $view->vars);

        self::assertSame('some_key', $view->vars['key_name']);
        self::assertSame('some_value', $view->vars['values_name']);

        self::assertArrayHasKey('some_key', $view->children);
        self::assertArrayHasKey('some_value', $view->children);
    }

    /**
     * @covers \Netgen\Layouts\Form\KeyValuesType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $constraint = new Constraints\NotBlank();

        $options = [
            'key_name' => 'some_key',
            'key_label' => 'some_key_label',
            'values_name' => 'some_values',
            'values_label' => 'some_values_label',
            'values_type' => 'some_type',
            'values_constraints' => [$constraint],
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        self::assertSame('some_key', $resolvedOptions['key_name']);
        self::assertSame('some_key_label', $resolvedOptions['key_label']);
        self::assertSame('some_values', $resolvedOptions['values_name']);
        self::assertSame('some_values_label', $resolvedOptions['values_label']);
        self::assertSame('some_type', $resolvedOptions['values_type']);
        self::assertSame([$constraint], $resolvedOptions['values_constraints']);
    }

    /**
     * @covers \Netgen\Layouts\Form\KeyValuesType::configureOptions
     */
    public function testConfigureOptionsWithInvalidConstraint(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "values_constraints" with value array is expected to be of type "Symfony\Component\Validator\Constraint[]", but one of the elements is of type "string".');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = [
            'key_name' => 'some_key',
            'key_label' => 'some_key_label',
            'values_name' => 'some_values',
            'values_label' => 'some_values_label',
            'values_type' => 'some_type',
            'values_constraints' => ['constraint'],
        ];

        $optionsResolver->resolve($options);
    }

    /**
     * @covers \Netgen\Layouts\Form\KeyValuesType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        self::assertSame('nglayouts_key_values', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        return new KeyValuesType();
    }
}
