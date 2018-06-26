<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Type;

use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper\Compound\BooleanMapper;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterStruct;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ParametersTypeTest extends FormTestCase
{
    public function getMainType(): FormTypeInterface
    {
        return new ParametersType(
            [
                'text_line' => new TextLineMapper(),
                'compound_boolean' => new BooleanMapper(),
            ]
        );
    }

    public function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper::handleForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::includeParameter
     */
    public function testSubmitValidData(): void
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

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct
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

        $parameterDefinitions = new ParameterDefinitionCollection(
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
                'parameter_definitions' => $parameterDefinitions,
                'label_prefix' => 'label',
                'property_path' => 'parameterValues',
            ]
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());

        $this->assertSame(
            [
                'css_class' => 'Some CSS class',
                'css_id' => 'Some CSS ID',
                'compound' => true,
                'inner' => 'Inner value',
            ],
            $struct->getParameterValues()
        );

        $this->assertCount(3, $parentForm->get('parameter_values')->all());

        foreach (array_keys($submittedData['parameter_values']) as $key) {
            $paramForm = $parentForm->get('parameter_values')->get($key);

            $this->assertSame('parameterValues[' . $key . ']', (string) $paramForm->getPropertyPath());
            $this->assertInstanceOf(
                $key === 'compound' ? CompoundBooleanType::class : TextType::class,
                $paramForm->getConfig()->getType()->getInnerType()
            );

            $this->assertSame(
                $parameterDefinitions->getParameterDefinition($key)->getLabel() ?? 'label.' . $key,
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
    public function testSubmitValidDataWithGroups(): void
    {
        $submittedData = [
            'parameter_values' => [
                'css_id' => 'Some CSS ID',
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct
        );

        $parameterDefinitions = new ParameterDefinitionCollection(
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
                'parameter_definitions' => $parameterDefinitions,
                'label_prefix' => 'label',
                'property_path' => 'parameterValues',
                'groups' => ['group'],
            ]
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());

        $this->assertSame(['css_id' => 'Some CSS ID'], $struct->getParameterValues());

        $this->assertCount(1, $parentForm->get('parameter_values')->all());
        $this->assertTrue($parentForm->get('parameter_values')->has('css_id'));
        $this->assertFalse($parentForm->get('parameter_values')->has('excluded'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $parameterDefinitions = new ParameterDefinitionCollection();

        $options = [
            'parameter_definitions' => $parameterDefinitions,
            'label_prefix' => 'label',
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertSame(
            [
                'translation_domain' => 'ngbm',
                'groups' => [],
                'parameter_definitions' => $parameterDefinitions,
                'label_prefix' => 'label',
            ],
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required options "label_prefix", "parameter_definitions" are missing.
     */
    public function testConfigureOptionsWithMissingParameters(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\ParametersType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "parameter_definitions" with value null is expected to be of type "Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface", but is of type "NULL".
     */
    public function testConfigureOptionsWithInvalidParameters(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'parameter_definitions' => null,
                'label_prefix' => 'label',
            ]
        );
    }
}
