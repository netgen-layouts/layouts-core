<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type;

use Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType;
use Netgen\Layouts\Tests\Parameters\Form\Type\Stubs\CompoundBoolean;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

#[CoversClass(CompoundBooleanType::class)]
final class CompoundBooleanTypeTest extends FormTestCase
{
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'main_checkbox' => [
                '_self' => '1',
                'param1' => 'param 1 value',
                'param2' => 'param 2 value',
            ],
        ];

        $data = new CompoundBoolean();

        $parentForm = $this->factory->create(
            FormType::class,
            $data,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'mainCheckbox',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param1',
            TextType::class,
            [
                'property_path' => 'param1',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param2',
            TextType::class,
            [
                'property_path' => 'param2',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertTrue($data->mainCheckbox);
        self::assertSame('param 1 value', $data->param1);
        self::assertSame('param 2 value', $data->param2);

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    public function testSubmitValidDataWithUncheckedCheckbox(): void
    {
        $submittedData = [
            'main_checkbox' => [
                'param1' => 'param 1 value',
                'param2' => 'param 2 value',
            ],
        ];

        $data = new CompoundBoolean();

        $parentForm = $this->factory->create(
            FormType::class,
            $data,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'mainCheckbox',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param1',
            TextType::class,
            [
                'property_path' => 'param1',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param2',
            TextType::class,
            [
                'property_path' => 'param2',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertFalse($data->mainCheckbox);
        self::assertNull($data->param1);
        self::assertNull($data->param2);

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    public function testSubmitValidDataWithUncheckedCheckboxAndEmptyData(): void
    {
        $submittedData = [
            'main_checkbox' => [],
        ];

        $data = new CompoundBoolean();

        $parentForm = $this->factory->create(
            FormType::class,
            $data,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'mainCheckbox',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param1',
            TextType::class,
            [
                'property_path' => 'param1',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param2',
            TextType::class,
            [
                'property_path' => 'param2',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertFalse($data->mainCheckbox);
        self::assertNull($data->param1);
        self::assertNull($data->param2);

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);
        self::assertArrayHasKey('param1', $children['main_checkbox']->children);
        self::assertArrayHasKey('param2', $children['main_checkbox']->children);
    }

    public function testSubmitValidDataWithReverseMode(): void
    {
        $submittedData = [
            'main_checkbox' => [
                '_self' => '1',
                'param1' => 'param 1 value',
                'param2' => 'param 2 value',
            ],
        ];

        $data = new CompoundBoolean();

        $parentForm = $this->factory->create(
            FormType::class,
            $data,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'mainCheckbox',
                'reverse' => true,
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param1',
            TextType::class,
            [
                'property_path' => 'param1',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param2',
            TextType::class,
            [
                'property_path' => 'param2',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertTrue($data->mainCheckbox);
        self::assertNull($data->param1);
        self::assertNull($data->param2);

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    public function testSubmitValidDataWithUncheckedCheckboxAndReverseMode(): void
    {
        $submittedData = [
            'main_checkbox' => [
                'param1' => 'param 1 value',
                'param2' => 'param 2 value',
            ],
        ];

        $data = new CompoundBoolean();

        $parentForm = $this->factory->create(
            FormType::class,
            $data,
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'mainCheckbox',
                'reverse' => true,
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param1',
            TextType::class,
            [
                'property_path' => 'param1',
            ],
        );

        $parentForm->get('main_checkbox')->add(
            'param2',
            TextType::class,
            [
                'property_path' => 'param2',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertFalse($data->mainCheckbox);
        self::assertSame('param 1 value', $data->param1);
        self::assertSame('param 2 value', $data->param2);

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

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

    public function testGetBlockPrefix(): void
    {
        self::assertSame('nglayouts_compound_boolean', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        return new CompoundBooleanType();
    }
}
