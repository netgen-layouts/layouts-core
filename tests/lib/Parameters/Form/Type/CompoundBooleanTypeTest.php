<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type;

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
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormTypeInterface;

use function array_keys;

#[CoversClass(CompoundBooleanType::class)]
final class CompoundBooleanTypeTest extends FormTestCase
{
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'parameter_values' => [
                'main_checkbox' => [
                    '_self' => '1',
                    'css_id' => 'Some CSS ID',
                    'css_class' => 'Some CSS class',
                ],
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $compoundDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'main_checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
                'label' => null,
                'options' => [
                    'reverse' => false,
                ],
                'parameterDefinitions' => [
                    'css_class' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_class',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                    'css_id' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_id',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                ],
            ],
        );

        $parameterDefinitions = ParameterDefinitionCollection::fromArray(
            [
                'parameterDefinitions' => [
                    'main_checkbox' => $compoundDefinition,
                ],
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
                'main_checkbox' => true,
                'css_class' => 'Some CSS class',
                'css_id' => 'Some CSS ID',
            ],
            $struct->parameterValues,
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('parameter_values', $children);
        self::assertArrayHasKey('main_checkbox', $children['parameter_values']);

        foreach (array_keys($submittedData['parameter_values']['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['parameter_values']['main_checkbox']->children);
        }
    }

    public function testSubmitValidDataWithUncheckedCheckbox(): void
    {
        $submittedData = [
            'parameter_values' => [
                'main_checkbox' => [
                    'css_id' => 'Some CSS ID',
                    'css_class' => 'Some CSS class',
                ],
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $compoundDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'main_checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
                'label' => null,
                'options' => [
                    'reverse' => false,
                ],
                'parameterDefinitions' => [
                    'css_class' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_class',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                    'css_id' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_id',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                ],
            ],
        );

        $parameterDefinitions = ParameterDefinitionCollection::fromArray(
            [
                'parameterDefinitions' => [
                    'main_checkbox' => $compoundDefinition,
                ],
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
                'main_checkbox' => false,
                'css_class' => null,
                'css_id' => null,
            ],
            $struct->parameterValues,
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('parameter_values', $children);
        self::assertArrayHasKey('main_checkbox', $children['parameter_values']);

        foreach (array_keys($submittedData['parameter_values']['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['parameter_values']['main_checkbox']->children);
        }
    }

    public function testSubmitValidDataWithUncheckedCheckboxAndEmptyData(): void
    {
        $submittedData = [
            'parameter_values' => [
                'main_checkbox' => [],
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $compoundDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'main_checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
                'label' => null,
                'options' => [
                    'reverse' => false,
                ],
                'parameterDefinitions' => [
                    'css_class' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_class',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                    'css_id' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_id',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                ],
            ],
        );

        $parameterDefinitions = ParameterDefinitionCollection::fromArray(
            [
                'parameterDefinitions' => [
                    'main_checkbox' => $compoundDefinition,
                ],
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
                'main_checkbox' => false,
                'css_class' => null,
                'css_id' => null,
            ],
            $struct->parameterValues,
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('parameter_values', $children);
        self::assertArrayHasKey('main_checkbox', $children['parameter_values']);
        self::assertArrayHasKey('css_class', $children['parameter_values']['main_checkbox']->children);
        self::assertArrayHasKey('css_id', $children['parameter_values']['main_checkbox']->children);
    }

    public function testSubmitValidDataWithReverseMode(): void
    {
        $submittedData = [
            'parameter_values' => [
                'main_checkbox' => [
                    '_self' => '1',
                    'css_id' => 'Some CSS ID',
                    'css_class' => 'Some CSS class',
                ],
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $compoundDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'main_checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
                'label' => null,
                'options' => [
                    'reverse' => true,
                ],
                'parameterDefinitions' => [
                    'css_class' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_class',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                    'css_id' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_id',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                ],
            ],
        );

        $parameterDefinitions = ParameterDefinitionCollection::fromArray(
            [
                'parameterDefinitions' => [
                    'main_checkbox' => $compoundDefinition,
                ],
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
                'main_checkbox' => true,
                'css_class' => null,
                'css_id' => null,
            ],
            $struct->parameterValues,
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('parameter_values', $children);
        self::assertArrayHasKey('main_checkbox', $children['parameter_values']);

        foreach (array_keys($submittedData['parameter_values']['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['parameter_values']['main_checkbox']->children);
        }
    }

    public function testSubmitValidDataWithUncheckedCheckboxAndReverseMode(): void
    {
        $submittedData = [
            'parameter_values' => [
                'main_checkbox' => [
                    'css_id' => 'Some CSS ID',
                    'css_class' => 'Some CSS class',
                ],
            ],
        ];

        $struct = new ParameterStruct();

        $parentForm = $this->factory->create(
            FormType::class,
            $struct,
        );

        $compoundDefinition = ParameterDefinition::fromArray(
            [
                'name' => 'main_checkbox',
                'type' => new ParameterType\Compound\BooleanType(),
                'label' => null,
                'options' => [
                    'reverse' => true,
                ],
                'parameterDefinitions' => [
                    'css_class' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_class',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                    'css_id' => ParameterDefinition::fromArray(
                        [
                            'name' => 'css_id',
                            'type' => new ParameterType\TextLineType(),
                            'label' => null,
                        ],
                    ),
                ],
            ],
        );

        $parameterDefinitions = ParameterDefinitionCollection::fromArray(
            [
                'parameterDefinitions' => [
                    'main_checkbox' => $compoundDefinition,
                ],
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
                'main_checkbox' => false,
                'css_class' => 'Some CSS class',
                'css_id' => 'Some CSS ID',
            ],
            $struct->parameterValues,
        );

        $view = $parentForm->createView();
        $children = $view->children;

        self::assertArrayHasKey('parameter_values', $children);
        self::assertArrayHasKey('main_checkbox', $children['parameter_values']);

        foreach (array_keys($submittedData['parameter_values']['main_checkbox']) as $key) {
            self::assertArrayHasKey($key, $children['parameter_values']['main_checkbox']->children);
        }
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
