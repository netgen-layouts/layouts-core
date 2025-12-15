<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config\Form;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigList;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Config\Form\EditType;
use Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Netgen\Layouts\Tests\API\Stubs\ConfigAwareStruct;
use Netgen\Layouts\Tests\API\Stubs\ConfigAwareValue;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\Layouts\Tests\Parameters\Stubs\FormMapper;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[CoversClass(EditType::class)]
final class EditTypeTest extends FormTestCase
{
    private ConfigAwareValue $configurable;

    protected function setUp(): void
    {
        parent::setUp();

        $handler = new ConfigDefinitionHandler();

        $this->configurable = ConfigAwareValue::fromArray(
            [
                'configs' => new ConfigList(
                    [
                        'test' => Config::fromArray(
                            [
                                'definition' => ConfigDefinition::fromArray(
                                    [
                                        'parameterDefinitions' => $handler->getParameterDefinitions(),
                                    ],
                                ),
                            ],
                        ),
                    ],
                ),
            ],
        );
    }

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
            ],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('new_value', $configStruct->parameterValues['param']);
        self::assertNull($configStruct->parameterValues['param2']);

        $view = $form->createView();
        $children = $view->children;

        self::assertArrayHasKey('test', $children);
        self::assertArrayHasKey('param', $children['test']);

        self::assertArrayHasKey('configurable', $view->vars);
        self::assertSame($this->configurable, $view->vars['configurable']);
    }

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
            ],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('new_value', $configStruct->parameterValues['param']);
        self::assertNull($configStruct->parameterValues['param2']);

        $view = $form->createView();
        $children = $view->children;

        self::assertArrayHasKey('test', $children);
        self::assertArrayHasKey('param', $children['test']);

        self::assertArrayHasKey('configurable', $view->vars);
        self::assertSame($this->configurable, $view->vars['configurable']);
    }

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
            ],
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

    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new ConfigAwareStruct();

        $options = $optionsResolver->resolve(
            [
                'configurable' => $this->configurable,
                'label_prefix' => 'config.configurable',
                'data' => $struct,
            ],
        );

        self::assertSame($this->configurable, $options['configurable']);
        self::assertSame($struct, $options['data']);
    }

    public function testConfigureOptionsWithMissingValue(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "configurable" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'label_prefix' => 'config.configurable',
            ],
        );
    }

    public function testConfigureOptionsWithInvalidValue(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "configurable" with value "" is expected to be of type "Netgen\Layouts\API\Values\Config\ConfigAwareValue", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'configurable' => '',
                'label_prefix' => 'config.configurable',
            ],
        );
    }

    public function testConfigureOptionsWithMissingLabelPrefix(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "label_prefix" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'configurable' => $this->configurable,
            ],
        );
    }

    public function testConfigureOptionsWithInvalidLabelPrefix(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "label_prefix" with value 42 is expected to be of type "string", but is of type "int".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'configurable' => $this->configurable,
                'label_prefix' => 42,
            ],
        );
    }

    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\Config\ConfigAwareStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'configurable' => $this->configurable,
                'label_prefix' => 'config.configurable',
                'data' => '',
            ],
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
        return [new ParametersType(new Container(['text_line' => new FormMapper()]))];
    }
}
