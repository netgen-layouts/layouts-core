<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type;

use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\Layouts\Parameters\Form\Mapper\Compound\BooleanMapper;
use Netgen\Layouts\Parameters\Form\Mapper\TextLineMapper;
use Netgen\Layouts\Parameters\Form\Type\CompoundBooleanType;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Tests\API\Stubs\ParameterStruct;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

final class ParametersTypeTest extends FormTestCase
{
    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper::handleForm
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::__construct
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::buildForm
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::getMapper
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::includeParameter
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
            $struct,
        );

        $compoundParameter = CompoundParameterDefinition::fromArray(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'options' => [
                    'reverse' => false,
                ],
                'parameterDefinitions' => [
                    'inner' => ParameterDefinition::fromArray(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                        ],
                    ),
                ],
            ],
        );

        $parameterDefinitions = new ParameterDefinitionCollection(
            [
                'css_class' => ParameterDefinition::fromArray(
                    [
                        'name' => 'css_class',
                        'type' => new ParameterType\TextLineType(),
                        'label' => false,
                    ],
                ),
                'css_id' => ParameterDefinition::fromArray(
                    [
                        'name' => 'css_id',
                        'type' => new ParameterType\TextLineType(),
                        'label' => 'custom label',
                    ],
                ),
                'compound' => $compoundParameter,
            ],
        );

        $parentForm->add(
            'parameter_values',
            ParametersType::class,
            [
                'inherit_data' => true,
                'parameter_definitions' => $parameterDefinitions,
                'label_prefix' => 'label',
                'property_path' => 'parameterValues',
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertSame(
            [
                'css_class' => 'Some CSS class',
                'css_id' => 'Some CSS ID',
                'compound' => true,
                'inner' => 'Inner value',
            ],
            $struct->getParameterValues(),
        );

        self::assertCount(3, $parentForm->get('parameter_values')->all());

        foreach (array_keys($submittedData['parameter_values']) as $key) {
            $paramForm = $parentForm->get('parameter_values')->get($key);

            self::assertSame('parameterValues[' . $key . ']', (string) $paramForm->getPropertyPath());
            self::assertInstanceOf(
                $key === 'compound' ? CompoundBooleanType::class : TextType::class,
                $paramForm->getConfig()->getType()->getInnerType(),
            );

            self::assertSame(
                $parameterDefinitions->getParameterDefinition($key)->getLabel() ?? 'label.' . $key,
                $paramForm->getConfig()->getOption('label'),
            );
        }

        // View test

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('parameter_values', $children);

        foreach (array_keys($submittedData['parameter_values']) as $key) {
            self::assertArrayHasKey($key, $children['parameter_values']);
        }
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::buildForm
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::getMapper
     */
    public function testBuildFormWithNoMapper(): void
    {
        $this->expectException(ParameterTypeException::class);
        $this->expectExceptionMessage('Form mapper for "text" parameter type does not exist.');

        $parentForm = $this->factory->create(
            FormType::class,
            new ParameterStruct(),
        );

        $parameterDefinitions = new ParameterDefinitionCollection(
            [
                'test' => ParameterDefinition::fromArray(
                    [
                        'name' => 'test',
                        'type' => new ParameterType\TextType(),
                    ],
                ),
            ],
        );

        $parentForm->add(
            'parameter_values',
            ParametersType::class,
            [
                'inherit_data' => true,
                'parameter_definitions' => $parameterDefinitions,
                'label_prefix' => 'label',
                'property_path' => 'parameterValues',
            ],
        );

        $parentForm->submit([]);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::buildForm
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::getMapper
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::includeParameter
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
            $struct,
        );

        $parameterDefinitions = new ParameterDefinitionCollection(
            [
                'excluded' => ParameterDefinition::fromArray(
                    [
                        'name' => 'excluded',
                        'type' => new ParameterType\TextLineType(),
                        'groups' => ['excluded'],
                    ],
                ),
                'css_id' => ParameterDefinition::fromArray(
                    [
                        'name' => 'css_id',
                        'type' => new ParameterType\TextLineType(),
                        'groups' => ['group'],
                    ],
                ),
            ],
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
            ],
        );

        $parentForm->submit($submittedData);

        self::assertTrue($parentForm->isSynchronized());

        self::assertSame(['css_id' => 'Some CSS ID'], $struct->getParameterValues());

        self::assertCount(1, $parentForm->get('parameter_values')->all());
        self::assertTrue($parentForm->get('parameter_values')->has('css_id'));
        self::assertFalse($parentForm->get('parameter_values')->has('excluded'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::configureOptions
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

        self::assertSame(
            [
                'translation_domain' => 'nglayouts',
                'groups' => [],
                'parameter_definitions' => $parameterDefinitions,
                'label_prefix' => 'label',
            ],
            $resolvedOptions,
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::configureOptions
     */
    public function testConfigureOptionsWithMissingParameters(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required options "label_prefix", "parameter_definitions" are missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::configureOptions
     */
    public function testConfigureOptionsWithInvalidParameters(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessageMatches('/^The option "parameter_definitions" with value null is expected to be of type "Netgen\\\Layouts\\\Parameters\\\ParameterDefinitionCollectionInterface", but is of type "(NULL|null)".$/');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'parameter_definitions' => null,
                'label_prefix' => 'label',
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\ParametersType::configureOptions
     */
    public function testConfigureOptionsWithInvalidGroup(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessageMatches('/^The option "groups" with value array is expected to be of type "string\[\]", but one of the elements is of type "int(eger)?".$/');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'parameter_definitions' => new ParameterDefinitionCollection(),
                'label_prefix' => 'label',
                'groups' => [42],
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new ParametersType(
            new Container(
                [
                    'text_line' => new TextLineMapper(),
                    'compound_boolean' => new BooleanMapper(),
                ],
            ),
        );
    }

    protected function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }
}
