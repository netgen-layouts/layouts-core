<?php

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\Form\ContentEditType;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentEditTypeTest extends FormTestCase
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

        $config = new Configuration(
            array(
                'identifier' => 'block_definition',
                'forms' => array(
                    'content' => new Form(
                        array(
                            'identifier' => 'content',
                            'type' => ContentEditType::class,
                        )
                    ),
                ),
                'viewTypes' => array(
                    'large' => new ViewType(
                        array(
                            'identifier' => 'large',
                            'name' => 'Large',
                            'itemViewTypes' => array(
                                'standard' => new ItemViewType(
                                    array(
                                        'identifier' => 'standard',
                                        'name' => 'Standard',
                                    )
                                ),
                            ),
                        )
                    ),
                    'small' => new ViewType(
                        array(
                            'identifier' => 'large',
                            'name' => 'Large',
                            'itemViewTypes' => array(
                                'standard' => new ItemViewType(
                                    array(
                                        'identifier' => 'standard',
                                        'name' => 'Standard',
                                    )
                                ),
                            ),
                        )
                    ),
                ),
            )
        );

        $handler = new BlockDefinitionHandler(array('content'));
        $blockDefinition = new BlockDefinition(
            array(
                'identifier' => 'block_definition',
                'handler' => $handler,
                'config' => $config,
                'parameters' => $handler->getParameters(),
            )
        );

        $this->block = new Block(array('definition' => $blockDefinition));
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new ContentEditType();
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
        $formMapperRegistry->addFormMapper('text_line', new TextLineMapper());

        return array(new ParametersType($formMapperRegistry));
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'parameters' => array(
                'css_class' => 'Some CSS class',
            ),
            'name' => 'My block',
        );

        $updatedStruct = new BlockUpdateStruct();
        $updatedStruct->name = 'My block';
        $updatedStruct->setParameterValue('css_class', 'Some CSS class');

        $form = $this->factory->create(
            ContentEditType::class,
            new BlockUpdateStruct(),
            array('block' => $this->block)
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        foreach (array_keys($submittedData['parameters']) as $key) {
            $this->assertArrayHasKey($key, $children['parameters']);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'block' => $this->block,
                'data' => new BlockUpdateStruct(),
            )
        );

        $this->assertEquals($this->block, $options['block']);
        $this->assertEquals(new BlockUpdateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingBlockDefinition()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidBlockDefinition()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'block' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'block' => $this->block,
                'data' => '',
            )
        );
    }
}
