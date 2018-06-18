<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Form;

use Netgen\BlockManager\Form\KeyValuesType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class KeyValuesTypeTest extends FormTestCase
{
    public function getMainType(): FormTypeInterface
    {
        return new KeyValuesType();
    }

    /**
     * @covers \Netgen\BlockManager\Form\KeyValuesType::buildForm
     * @covers \Netgen\BlockManager\Form\KeyValuesType::buildView
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
            ]
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($submittedData, $form->getData());

        $this->assertSame('Key', $form->get('some_key')->getConfig()->getOption('label'));
        $this->assertSame('Value', $form->get('some_value')->getConfig()->getOption('label'));

        $this->assertInstanceOf(
            CollectionType::class,
            $form->get('some_value')->getConfig()->getType()->getInnerType()
        );

        $view = $form->createView();

        $this->assertArrayHasKey('key_name', $view->vars);
        $this->assertArrayHasKey('values_name', $view->vars);

        $this->assertSame('some_key', $view->vars['key_name']);
        $this->assertSame('some_value', $view->vars['values_name']);

        $this->assertArrayHasKey('some_key', $view->children);
        $this->assertArrayHasKey('some_value', $view->children);
    }

    /**
     * @covers \Netgen\BlockManager\Form\KeyValuesType::configureOptions
     */
    public function testConfigureOptions(): void
    {
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

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertSame('some_key', $resolvedOptions['key_name']);
        $this->assertSame('some_key_label', $resolvedOptions['key_label']);
        $this->assertSame('some_values', $resolvedOptions['values_name']);
        $this->assertSame('some_values_label', $resolvedOptions['values_label']);
        $this->assertSame('some_type', $resolvedOptions['values_type']);
        $this->assertSame(['constraint'], $resolvedOptions['values_constraints']);
    }

    /**
     * @covers \Netgen\BlockManager\Form\KeyValuesType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        $this->assertSame('ngbm_key_values', $this->formType->getBlockPrefix());
    }
}
