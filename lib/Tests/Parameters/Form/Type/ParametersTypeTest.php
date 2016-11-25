<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Type;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametersTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $formMapperRegistry = new FormMapperRegistry();
        $formMapperRegistry->addFormMapper('text_line', new TextLineMapper());
        $formMapperRegistry->addFormMapper('compound_boolean', new BooleanMapper());

        return new ParametersType($formMapperRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::includeParameter
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'parameter_values' => array(
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
                'compound' => array(
                    '_self' => true,
                    'inner' => 'Inner value',
                ),
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameterValue('css_id', 'Some CSS ID');
        $updatedStruct->setParameterValue('css_class', 'Some CSS class');
        $updatedStruct->setParameterValue('compound', true);
        $updatedStruct->setParameterValue('inner', 'Inner value');

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $compoundParameter = new CompoundParameter('compound', new ParameterType\Compound\BooleanType());
        $compoundParameter->setParameters(
            array(
                'inner' => new Parameter('inner', new ParameterType\TextLineType()),
            )
        );

        $parameterCollection = new ParameterCollection(
            array(
                'css_class' => new Parameter('css_class', new ParameterType\TextLineType()),
                'css_id' => new Parameter('css_id', new ParameterType\TextLineType()),
                'compound' => $compoundParameter,
            )
        );

        $parentForm->add(
            'parameter_values',
            ParametersType::class,
            array(
                'parameter_collection' => $parameterCollection,
                'label_prefix' => 'label',
                'property_path' => 'parameterValues',
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $this->assertCount(3, $parentForm->get('parameter_values')->all());

        foreach (array_keys($submittedData['parameter_values']) as $key) {
            $paramForm = $parentForm->get('parameter_values')->get($key);

            $this->assertEquals('parameterValues[' . $key . ']', $paramForm->getPropertyPath());
            $this->assertEquals('label.' . $key, $paramForm->getConfig()->getOption('label'));
            $this->assertInstanceOf(
                $key === 'compound' ? CompoundBooleanType::class : TextType::class,
                $paramForm->getConfig()->getType()->getInnerType()
            );
        }

        // View test

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('parameter_values', $children);

        foreach (array_keys($submittedData['parameter_values']) as $key) {
            $this->assertArrayHasKey($key, $children['parameter_values']);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::includeParameter
     */
    public function testSubmitValidDataWithGroups()
    {
        $submittedData = array(
            'parameter_values' => array(
                'css_id' => 'Some CSS ID',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameterValue('css_id', 'Some CSS ID');

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parameterCollection = new ParameterCollection(
            array(
                'excluded' => new Parameter('excluded', new ParameterType\TextLineType()),
                'css_id' => new Parameter('css_id', new ParameterType\TextLineType(), array(), false, null, array('group')),
            )
        );

        $parentForm->add(
            'parameter_values',
            ParametersType::class,
            array(
                'parameter_collection' => $parameterCollection,
                'label_prefix' => 'label',
                'property_path' => 'parameterValues',
                'groups' => array('group'),
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $this->assertCount(1, $parentForm->get('parameter_values')->all());
        $this->assertTrue($parentForm->get('parameter_values')->has('css_id'));
        $this->assertFalse($parentForm->get('parameter_values')->has('excluded'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = array(
            'parameter_collection' => new ParameterCollection(),
            'label_prefix' => 'label',
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals(
            array(
                'parameter_collection' => new ParameterCollection(),
                'label_prefix' => 'label',
                'groups' => array(),
                'inherit_data' => true,
                'translation_domain' => ParametersType::TRANSLATION_DOMAIN,
            ),
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'parameter_collection' => null,
                'label_prefix' => 'label',
            )
        );
    }
}
