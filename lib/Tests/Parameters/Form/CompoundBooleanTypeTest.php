<?php

namespace Netgen\BlockManager\Tests\Parameters\Form;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\Form\CompoundBooleanType;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Parameters\Parameter\TextLine as TextLineParameter;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompoundBooleanTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $formMapper = new FormMapper(
            new ParameterFilterRegistry(),
            array(
                'text_line' => new TextLine(),
            )
        );

        return new CompoundBooleanType($formMapper);
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
        $updatedStruct->setParameter('main_checkbox', true);
        $updatedStruct->setParameter('css_id', 'Some CSS ID');
        $updatedStruct->setParameter('css_class', 'Some CSS class');

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'parameters' => array(
                    'css_class' => new TextLineParameter(),
                    'css_id' => new TextLineParameter(),
                ),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'checkbox_label' => 'checkbox_label',
                'checkbox_property_path' => 'parameters[main_checkbox]',
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        self::assertArrayHasKey('checkbox_name', $view->children['main_checkbox']->vars);
        self::assertEquals('_self', $view->children['main_checkbox']->vars['checkbox_name']);

        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
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
        $updatedStruct->setParameter('main_checkbox', false);

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'parameters' => array(
                    'css_class' => new TextLineParameter(),
                    'css_id' => new TextLineParameter(),
                ),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'checkbox_label' => 'checkbox_label',
                'checkbox_property_path' => 'parameters[main_checkbox]',
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        self::assertArrayHasKey('checkbox_name', $view->children['main_checkbox']->vars);
        self::assertEquals('_self', $view->children['main_checkbox']->vars['checkbox_name']);

        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
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
        $updatedStruct->setParameter('main_checkbox', false);

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'parameters' => array(
                    'css_class' => new TextLineParameter(),
                    'css_id' => new TextLineParameter(),
                ),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'checkbox_label' => 'checkbox_label',
                'checkbox_property_path' => 'parameters[main_checkbox]',
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        self::assertArrayHasKey('checkbox_name', $view->children['main_checkbox']->vars);
        self::assertEquals('_self', $view->children['main_checkbox']->vars['checkbox_name']);

        $children = $view->children;

        self::assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['main_checkbox']->children);
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
            'parameters' => array(),
            'label_prefix' => 'label',
            'property_path_prefix' => 'parameters',
            'checkbox_label' => 'checkbox_label',
            'checkbox_property_path' => 'checkbox_property_path',
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        self::assertTrue($resolvedOptions['inherit_data']);
        self::assertEquals(array(), $resolvedOptions['parameters']);
        self::assertEquals('label', $resolvedOptions['label_prefix']);
        self::assertEquals('parameters', $resolvedOptions['property_path_prefix']);
        self::assertNull($resolvedOptions['parameter_validation_groups']);
        self::assertEquals('checkbox_label', $resolvedOptions['checkbox_label']);
        self::assertEquals('checkbox_property_path', $resolvedOptions['checkbox_property_path']);
        self::assertEquals('_self', $resolvedOptions['checkbox_name']);
        self::assertFalse($resolvedOptions['checkbox_required']);
        self::assertEquals(array(), $resolvedOptions['checkbox_constraints']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'parameters' => null,
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'checkbox_label' => 'checkbox_label',
                'checkbox_property_path' => 'checkbox_property_path',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'parameters' => array(),
                'label_prefix' => 'label',
                'property_path_prefix' => null,
                'checkbox_label' => 'checkbox_label',
                'checkbox_property_path' => 'checkbox_property_path',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('ngbm_compound_boolean', $this->formType->getBlockPrefix());
    }
}
