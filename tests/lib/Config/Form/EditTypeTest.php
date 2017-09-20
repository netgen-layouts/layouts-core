<?php

namespace Netgen\BlockManager\Tests\Config\Form;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\Form\EditType;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Config\Stubs\Block\DisabledConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Block\Block
     */
    private $block;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $blockDefinition = new BlockDefinition(
            'block_definition',
            array('large' => array('standard'), 'small' => array('standard'))
        );

        $this->block = new Block(
            array(
                'definition' => $blockDefinition,
                'configs' => array(
                    'disabled' => new Config(
                        array(
                            'definition' => new ConfigDefinition(
                                'disabled',
                                new DisabledConfigHandler()
                            ),
                        )
                    ),
                    'http_cache' => new Config(
                        array(
                            'definition' => new ConfigDefinition(
                                'http_cache',
                                new HttpCacheConfigHandler()
                            ),
                        )
                    ),
                ),
            )
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
        return array(new ParametersTypeExtension());
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getTypes()
    {
        $formMapperRegistry = new FormMapperRegistry();
        $formMapperRegistry->addFormMapper('boolean', new Mapper\BooleanMapper());
        $formMapperRegistry->addFormMapper('integer', new Mapper\IntegerMapper());
        $formMapperRegistry->addFormMapper('text_line', new Mapper\TextLineMapper());

        return array(new ParametersType($formMapperRegistry));
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildForm
     * @covers \Netgen\BlockManager\Config\Form\EditType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'http_cache' => array(
                'use_http_cache' => true,
                'shared_max_age' => 300,
            ),
        );

        $updatedStruct = new BlockUpdateStruct();

        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('use_http_cache', true);
        $configStruct->setParameterValue('shared_max_age', 300);

        $updatedStruct->setConfigStruct('disabled', new ConfigStruct());
        $updatedStruct->setConfigStruct('http_cache', $configStruct);

        $struct = new BlockUpdateStruct();
        $struct->setConfigStruct('disabled', new ConfigStruct());
        $struct->setConfigStruct('http_cache', new ConfigStruct());

        $form = $this->factory->create(
            EditType::class,
            $struct,
            array(
                'configurable' => $this->block,
                'label_prefix' => 'config.block',
            )
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        $this->assertArrayNotHasKey('disabled', $children);

        $this->assertArrayHasKey('http_cache', $children);
        $this->assertArrayHasKey('use_http_cache', $children['http_cache']);
        $this->assertArrayHasKey('shared_max_age', $children['http_cache']);

        $this->assertArrayHasKey('configurable', $view->vars);
        $this->assertEquals($this->block, $view->vars['configurable']);
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
            array(
                'configurable' => $this->block,
                'label_prefix' => 'config.block',
                'data' => new BlockUpdateStruct(),
            )
        );

        $this->assertEquals($this->block, $options['configurable']);
        $this->assertEquals(new BlockUpdateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingValue()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'label_prefix' => 'config.block',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidValue()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'configurable' => '',
                'label_prefix' => 'config.block',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingLabelPrefix()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'configurable' => $this->block,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidLabelPrefix()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'configurable' => $this->block,
                'label_prefix' => 42,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'configurable' => $this->block,
                'label_prefix' => 'config.block',
                'data' => '',
            )
        );
    }
}
