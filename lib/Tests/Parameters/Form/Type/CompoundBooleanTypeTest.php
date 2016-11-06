<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Type;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompoundBooleanTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new CompoundBooleanType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'main_checkbox' => array(
                '_self' => '1',
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameterValue('main_checkbox', true);
        $updatedStruct->setParameterValue('css_id', 'Some CSS ID');
        $updatedStruct->setParameterValue('css_class', 'Some CSS class');

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'property_path' => 'parameterValues[main_checkbox]',
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_class]',
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_id]',
            )
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
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckbox()
    {
        $submittedData = array(
            'main_checkbox' => array(
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameterValue('main_checkbox', false);

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'property_path' => 'parameterValues[main_checkbox]',
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_class]',
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_id]',
            )
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
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckboxAndEmptyData()
    {
        $submittedData = array(
            'main_checkbox' => array(),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameterValue('main_checkbox', false);

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'property_path' => 'parameterValues[main_checkbox]',
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_class]',
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_id]',
            )
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
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithReverseMode()
    {
        $submittedData = array(
            'main_checkbox' => array(
                '_self' => '1',
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameterValue('main_checkbox', true);

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'property_path' => 'parameterValues[main_checkbox]',
                'reverse' => true,
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_class]',
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_id]',
            )
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
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckboxAndReverseMode()
    {
        $submittedData = array(
            'main_checkbox' => array(
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameterValue('main_checkbox', false);
        $updatedStruct->setParameterValue('css_id', 'Some CSS ID');
        $updatedStruct->setParameterValue('css_class', 'Some CSS class');

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'property_path' => 'parameterValues[main_checkbox]',
                'reverse' => true,
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_class',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_class]',
            )
        );

        $parentForm->get('main_checkbox')->add(
            'css_id',
            TextType::class,
            array(
                'property_path' => 'parameterValues[css_id]',
            )
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
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = array(
            'reverse' => true,
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertTrue($resolvedOptions['inherit_data']);
        $this->assertTrue($resolvedOptions['reverse']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ngbm_compound_boolean', $this->formType->getBlockPrefix());
    }
}
