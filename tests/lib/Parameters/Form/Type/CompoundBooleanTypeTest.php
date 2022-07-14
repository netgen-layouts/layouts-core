<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type;

use Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType;
use Netgen\Layouts\Tests\API\Stubs\ParameterStruct;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

final class CompoundBooleanTypeTest extends FormTestCase
{
    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'main_checkbox' => [
                '_self' => '1',
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertSame(
            [
                'main_checkbox' => true,
                'css_class' => 'Some CSS class',
                'css_id' => 'Some CSS ID',
            ],
            $struct->getParameterValues(),
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckbox(): void
    {
        $submittedData = [
            'main_checkbox' => [
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertSame(
            [
                'main_checkbox' => false,
            ],
            $struct->getParameterValues(),
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckboxAndEmptyData(): void
    {
        $submittedData = [
            'main_checkbox' => [],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertSame(
            [
                'main_checkbox' => false,
            ],
            $struct->getParameterValues(),
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);
        self::assertArrayHasKey('css_class', $children['main_checkbox']->children);
        self::assertArrayHasKey('css_id', $children['main_checkbox']->children);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithReverseMode(): void
    {
        $submittedData = [
            'main_checkbox' => [
                '_self' => '1',
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
                'reverse' => true,
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertSame(
            [
                'main_checkbox' => true,
            ],
            $struct->getParameterValues(),
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckboxAndReverseMode(): void
    {
        $submittedData = [
            'main_checkbox' => [
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
                'reverse' => true,
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertSame(
            [
                'main_checkbox' => false,
                'css_class' => 'Some CSS class',
                'css_id' => 'Some CSS ID',
            ],
            $struct->getParameterValues(),
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = [
            'reverse' => true,
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        self::assertTrue($resolvedOptions['inherit_data']);
        self::assertTrue($resolvedOptions['reverse']);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        self::assertSame('nglayouts_compound_boolean', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        return new CompoundBooleanType();
    }
}
