<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Config\Form;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Config\Form\EditType;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Tests\API\Stubs\ConfigAwareStruct;
use Netgen\BlockManager\Tests\API\Stubs\ConfigAwareValue;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\Parameters\Stubs\FormMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigAwareValue
     */
    private $configurable;

    public function setUp(): void
    {
        parent::setUp();

        $handler = new ConfigDefinitionHandler();

        $this->configurable = ConfigAwareValue::fromArray(
            [
                'configs' => [
                    'test' => Config::fromArray(
                        [
                            'definition' => ConfigDefinition::fromArray(
                                [
                                    'parameterDefinitions' => $handler->getParameterDefinitions(),
                                ]
                            ),
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildForm
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'test' => [
                'param' => 'new_value',
            ],
        ];

        $configStruct = new ConfigStruct();

        $struct = new ConfigAwareStruct();
        $struct->setConfigStruct('test', $configStruct);

        $form = $this->factory->create(
            EditType::class,
            $struct,
            [
                'configurable' => $this->configurable,
                'label_prefix' => 'config.configurable',
            ]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame(['param' => 'new_value'], $configStruct->getParameterValues());

        $view = $form->createView();
        $children = $view->children;

        self::assertArrayHasKey('test', $children);
        self::assertArrayHasKey('param', $children['test']);

        self::assertArrayHasKey('configurable', $view->vars);
        self::assertSame($this->configurable, $view->vars['configurable']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildForm
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildView
     */
    public function testSubmitValidDataWithConfigKey(): void
    {
        $submittedData = [
            'test' => [
                'param' => 'new_value',
            ],
        ];

        $configStruct = new ConfigStruct();

        $struct = new ConfigAwareStruct();
        $struct->setConfigStruct('test', $configStruct);

        $form = $this->factory->create(
            EditType::class,
            $struct,
            [
                'configurable' => $this->configurable,
                'config_key' => 'test',
                'label_prefix' => 'config.configurable',
            ]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame(['param' => 'new_value'], $configStruct->getParameterValues());

        $view = $form->createView();
        $children = $view->children;

        self::assertArrayHasKey('test', $children);
        self::assertArrayHasKey('param', $children['test']);

        self::assertArrayHasKey('configurable', $view->vars);
        self::assertSame($this->configurable, $view->vars['configurable']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildForm
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildView
     */
    public function testSubmitDataWithInvalidConfigKey(): void
    {
        $submittedData = [
            'test' => [
                'param' => 'new_value',
            ],
        ];

        $struct = new ConfigAwareStruct();
        $struct->setConfigStruct('test', new ConfigStruct());

        $form = $this->factory->create(
            EditType::class,
            $struct,
            [
                'configurable' => $this->configurable,
                'config_key' => 'unknown',
                'label_prefix' => 'config.configurable',
            ]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame($struct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        self::assertArrayNotHasKey('test', $children);
        self::assertArrayNotHasKey('unknown', $children);

        self::assertArrayHasKey('configurable', $view->vars);
        self::assertSame($this->configurable, $view->vars['configurable']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new ConfigAwareStruct();

        $options = $optionsResolver->resolve(
            [
                'configurable' => $this->configurable,
                'label_prefix' => 'config.configurable',
                'data' => $struct,
            ]
        );

        self::assertSame($this->configurable, $options['configurable']);
        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     */
    public function testConfigureOptionsWithMissingValue(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "configurable" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'label_prefix' => 'config.configurable',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidValue(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "configurable" with value "" is expected to be of type "Netgen\\BlockManager\\API\\Values\\Config\\ConfigAwareValue", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'configurable' => '',
                'label_prefix' => 'config.configurable',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     */
    public function testConfigureOptionsWithMissingLabelPrefix(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "label_prefix" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'configurable' => $this->configurable,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidLabelPrefix(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "label_prefix" with value 42 is expected to be of type "string", but is of type "integer".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'configurable' => $this->configurable,
                'label_prefix' => 42,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\\BlockManager\\API\\Values\\Config\\ConfigAwareStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'configurable' => $this->configurable,
                'label_prefix' => 'config.configurable',
                'data' => '',
            ]
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new EditType();
    }

    protected function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    protected function getTypes(): array
    {
        return [new ParametersType(['text_line' => new FormMapper()])];
    }
}
