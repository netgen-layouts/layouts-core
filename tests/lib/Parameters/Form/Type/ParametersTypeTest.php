<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Type;

use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterStruct;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ParametersTypeTest extends FormTestCase
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
     * @return \Symfony\Component\Form\FormTypeExtensionInterface[]
     */
    public function getTypeExtensions()
    {
        return [new ParametersTypeExtension()];
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper::handleForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::includeParameter
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'parameter_values' => [
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
                'compound' => [
                    '_self' => true,
                    'inner' => 'Inner value',
                ],
            ],
        ];

        $updatedStruct = new ParameterStruct();
        $updatedStruct->setParameterValue('css_id', 'Some CSS ID');
        $updatedStruct->setParameterValue('css_class', 'Some CSS class');
        $updatedStruct->setParameterValue('compound', true);
        $updatedStruct->setParameterValue('inner', 'Inner value');

        $parentForm = $this->factory->create(
            FormType::class,
            new ParameterStruct()
        );

        $compoundParameter = new CompoundParameterDefinition(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'options' => [
                    'reverse' => false,
                ],
                'parameterDefinitions' => [
                    'inner' => new ParameterDefinition(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                        ]
                    ),
                ],
            ]
        );

        $parameterCollection = new ParameterCollection(
            [
                'css_class' => new ParameterDefinition(
                    [
                        'name' => 'css_class',
                        'type' => new ParameterType\TextLineType(),
                        'label' => false,
                    ]
                ),
                'css_id' => new ParameterDefinition(
                    [
                        'name' => 'css_id',
                        'type' => new ParameterType\TextLineType(),
                        'label' => 'custom label',
                    ]
                ),
                'compound' => $compoundParameter,
            ]
        );

        $parentForm->add(
            'parameter_values',
            ParametersType::class,
            [
                'inherit_data' => true,
                'parameter_collection' => $parameterCollection,
                'label_prefix' => 'label',
                'property_path' => 'parameterValues',
            ]
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $this->assertCount(3, $parentForm->get('parameter_values')->all());

        foreach (array_keys($submittedData['parameter_values']) as $key) {
            $paramForm = $parentForm->get('parameter_values')->get($key);

            $this->assertEquals('parameterValues[' . $key . ']', $paramForm->getPropertyPath());
            $this->assertInstanceOf(
                $key === 'compound' ? CompoundBooleanType::class : TextType::class,
                $paramForm->getConfig()->getType()->getInnerType()
            );

            $this->assertEquals(
                $parameterCollection->getParameterDefinition($key)->getLabel() === null ?
                    'label.' . $key :
                    $parameterCollection->getParameterDefinition($key)->getLabel(),
                $paramForm->getConfig()->getOption('label')
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
        $submittedData = [
            'parameter_values' => [
                'css_id' => 'Some CSS ID',
            ],
        ];

        $updatedStruct = new ParameterStruct();
        $updatedStruct->setParameterValue('css_id', 'Some CSS ID');

        $parentForm = $this->factory->create(
            FormType::class,
            new ParameterStruct()
        );

        $parameterCollection = new ParameterCollection(
            [
                'excluded' => new ParameterDefinition(
                    [
                        'name' => 'excluded',
                        'type' => new ParameterType\TextLineType(),
                        'groups' => ['excluded'],
                    ]
                ),
                'css_id' => new ParameterDefinition(
                    [
                        'name' => 'css_id',
                        'type' => new ParameterType\TextLineType(),
                        'groups' => ['group'],
                    ]
                ),
            ]
        );

        $parentForm->add(
            'parameter_values',
            ParametersType::class,
            [
                'inherit_data' => true,
                'parameter_collection' => $parameterCollection,
                'label_prefix' => 'label',
                'property_path' => 'parameterValues',
                'groups' => ['group'],
            ]
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
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = [
            'parameter_collection' => new ParameterCollection(),
            'label_prefix' => 'label',
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals(
            [
                'parameter_collection' => new ParameterCollection(),
                'label_prefix' => 'label',
                'groups' => [],
                'translation_domain' => 'ngbm',
            ],
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required options "label_prefix", "parameter_collection" are missing.
     */
    public function testConfigureOptionsWithMissingParameters()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "parameter_collection" with value null is expected to be of type "Netgen\BlockManager\Parameters\ParameterCollectionInterface", but is of type "NULL".
     */
    public function testConfigureOptionsWithInvalidParameters()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'parameter_collection' => null,
                'label_prefix' => 'label',
            ]
        );
    }
}
