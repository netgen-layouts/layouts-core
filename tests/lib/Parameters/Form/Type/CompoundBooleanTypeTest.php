<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Type;

use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterStruct;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CompoundBooleanTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new CompoundBooleanType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'main_checkbox' => [
                '_self' => '1',
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
        ];

        $updatedStruct = new ParameterStruct();
        $updatedStruct->setParameterValue('main_checkbox', true);
        $updatedStruct->setParameterValue('css_id', 'Some CSS ID');
        $updatedStruct->setParameterValue('css_class', 'Some CSS class');

        $parentForm = $this->factory->create(
            FormType::class,
            new ParameterStruct()
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ]
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckbox()
    {
        $submittedData = [
            'main_checkbox' => [
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
        ];

        $updatedStruct = new ParameterStruct();
        $updatedStruct->setParameterValue('main_checkbox', false);

        $parentForm = $this->factory->create(
            FormType::class,
            new ParameterStruct()
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ]
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckboxAndEmptyData()
    {
        $submittedData = [
            'main_checkbox' => [],
        ];

        $updatedStruct = new ParameterStruct();
        $updatedStruct->setParameterValue('main_checkbox', false);

        $parentForm = $this->factory->create(
            FormType::class,
            new ParameterStruct()
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ]
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithReverseMode()
    {
        $submittedData = [
            'main_checkbox' => [
                '_self' => '1',
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
        ];

        $updatedStruct = new ParameterStruct();
        $updatedStruct->setParameterValue('main_checkbox', true);

        $parentForm = $this->factory->create(
            FormType::class,
            new ParameterStruct()
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
                'reverse' => true,
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ]
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckboxAndReverseMode()
    {
        $submittedData = [
            'main_checkbox' => [
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
        ];

        $updatedStruct = new ParameterStruct();
        $updatedStruct->setParameterValue('main_checkbox', false);
        $updatedStruct->setParameterValue('css_id', 'Some CSS ID');
        $updatedStruct->setParameterValue('css_class', 'Some CSS class');

        $parentForm = $this->factory->create(
            FormType::class,
            new ParameterStruct()
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            [
                'property_path' => 'parameterValues[main_checkbox]',
                'reverse' => true,
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_class]',
            ]
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            [
                'property_path' => 'parameterValues[css_id]',
            ]
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = [
            'reverse' => true,
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertTrue($resolvedOptions['inherit_data']);
        $this->assertTrue($resolvedOptions['reverse']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ngbm_compound_boolean', $this->formType->getBlockPrefix());
    }
}
