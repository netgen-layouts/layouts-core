<?php

namespace Netgen\BlockManager\Tests\Config\Form;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\Form\EditType;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareStruct;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareValue;
use Netgen\BlockManager\Tests\Parameters\Stubs\FormMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigAwareValue
     */
    private $configurable;

    public function setUp()
    {
        parent::setUp();

        $this->configurable = new ConfigAwareValue(
            [
                'configs' => [
                    'test' => new Config(
                        [
                            'definition' => new ConfigDefinition('test'),
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new EditType();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeExtensionInterface[]
     */
    public function getTypeExtensions()
    {
        return [new ParametersTypeExtension()];
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getTypes()
    {
        $formMapperRegistry = new FormMapperRegistry();
        $formMapperRegistry->addFormMapper('text_line', new FormMapper());

        return [new ParametersType($formMapperRegistry)];
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildForm
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'test' => [
                'param' => 'new_value',
            ],
        ];

        $updatedStruct = new ConfigAwareStruct();

        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('param', 'new_value');

        $updatedStruct->setConfigStruct('test', $configStruct);

        $struct = new ConfigAwareStruct();
        $struct->setConfigStruct('test', new ConfigStruct());

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
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        $this->assertArrayHasKey('test', $children);
        $this->assertArrayHasKey('param', $children['test']);

        $this->assertArrayHasKey('configurable', $view->vars);
        $this->assertEquals($this->configurable, $view->vars['configurable']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildForm
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildView
     */
    public function testSubmitValidDataWithConfigKey()
    {
        $submittedData = [
            'test' => [
                'param' => 'new_value',
            ],
        ];

        $updatedStruct = new ConfigAwareStruct();

        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('param', 'new_value');

        $updatedStruct->setConfigStruct('test', $configStruct);

        $struct = new ConfigAwareStruct();
        $struct->setConfigStruct('test', new ConfigStruct());

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
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        $this->assertArrayHasKey('test', $children);
        $this->assertArrayHasKey('param', $children['test']);

        $this->assertArrayHasKey('configurable', $view->vars);
        $this->assertEquals($this->configurable, $view->vars['configurable']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildForm
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildView
     */
    public function testSubmitDataWithInvalidConfigKey()
    {
        $submittedData = [
            'test' => [
                'param' => 'new_value',
            ],
        ];

        $updatedStruct = new ConfigAwareStruct();

        $configStruct = new ConfigStruct();
        $updatedStruct->setConfigStruct('test', $configStruct);

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
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        $this->assertArrayNotHasKey('test', $children);
        $this->assertArrayNotHasKey('unknown', $children);

        $this->assertArrayHasKey('configurable', $view->vars);
        $this->assertEquals($this->configurable, $view->vars['configurable']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'configurable' => $this->configurable,
                'label_prefix' => 'config.configurable',
                'data' => new ConfigAwareStruct(),
            ]
        );

        $this->assertEquals($this->configurable, $options['configurable']);
        $this->assertEquals(new ConfigAwareStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "configurable" is missing.
     */
    public function testConfigureOptionsWithMissingValue()
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
    public function testConfigureOptionsWithInvalidValue()
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
    public function testConfigureOptionsWithMissingLabelPrefix()
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
    public function testConfigureOptionsWithInvalidLabelPrefix()
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
    public function testConfigureOptionsWithInvalidData()
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
