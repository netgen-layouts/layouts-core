<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Config\Form;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Config\Form\EditType;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareStruct;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareValue;
use Netgen\BlockManager\Tests\Parameters\Stubs\FormMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
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

        $this->configurable = new ConfigAwareValue(
            [
                'configs' => [
                    'test' => new Config(
                        [
                            'definition' => new ConfigDefinition(
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

    public function getMainType(): FormTypeInterface
    {
        return new EditType();
    }

    public function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    public function getTypes(): array
    {
        return [new ParametersType(['text_line' => new FormMapper()])];
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

        $this->assertTrue($form->isSynchronized());

        $this->assertSame(['param' => 'new_value'], $configStruct->getParameterValues());

        $view = $form->createView();
        $children = $view->children;

        $this->assertArrayHasKey('test', $children);
        $this->assertArrayHasKey('param', $children['test']);

        $this->assertArrayHasKey('configurable', $view->vars);
        $this->assertSame($this->configurable, $view->vars['configurable']);
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

        $this->assertTrue($form->isSynchronized());

        $this->assertSame(['param' => 'new_value'], $configStruct->getParameterValues());

        $view = $form->createView();
        $children = $view->children;

        $this->assertArrayHasKey('test', $children);
        $this->assertArrayHasKey('param', $children['test']);

        $this->assertArrayHasKey('configurable', $view->vars);
        $this->assertSame($this->configurable, $view->vars['configurable']);
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

        $this->assertTrue($form->isSynchronized());

        $this->assertSame($struct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        $this->assertArrayNotHasKey('test', $children);
        $this->assertArrayNotHasKey('unknown', $children);

        $this->assertArrayHasKey('configurable', $view->vars);
        $this->assertSame($this->configurable, $view->vars['configurable']);
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

        $this->assertSame($this->configurable, $options['configurable']);
        $this->assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "configurable" is missing.
     */
    public function testConfigureOptionsWithMissingValue(): void
    {
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
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "configurable" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Config\ConfigAwareValue", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidValue(): void
    {
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
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "label_prefix" is missing.
     */
    public function testConfigureOptionsWithMissingLabelPrefix(): void
    {
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
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "label_prefix" with value 42 is expected to be of type "string", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidLabelPrefix(): void
    {
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
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Config\ConfigAwareStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
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
}
