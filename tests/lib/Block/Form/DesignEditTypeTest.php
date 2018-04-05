<?php

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\Form\DesignEditType;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DesignEditTypeTest extends FormTestCase
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

        $handler = new BlockDefinitionHandler(array('design'));
        $blockDefinition = new BlockDefinition(
            array(
                'identifier' => 'block_definition',
                'handler' => $handler,
                'forms' => array(
                    'design' => new Form(
                        array(
                            'identifier' => 'design',
                            'type' => DesignEditType::class,
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
                                'other' => new ItemViewType(
                                    array(
                                        'identifier' => 'other',
                                        'name' => 'Other',
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
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            )
        );

        $this->block = new Block(array('definition' => $blockDefinition));
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new DesignEditType();
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
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::buildView
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'parameters' => array(
                'css_class' => 'Some CSS class',
            ),
            'view_type' => 'large',
            'item_view_type' => 'standard',
        );

        $updatedStruct = new BlockUpdateStruct();
        $updatedStruct->viewType = 'large';
        $updatedStruct->itemViewType = 'standard';
        $updatedStruct->setParameterValue('css_class', 'Some CSS class');

        $form = $this->factory->create(
            DesignEditType::class,
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
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::configureOptions
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
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "block" is missing.
     */
    public function testConfigureOptionsWithMissingBlock()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "block" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Block\Block", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidBlock()
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
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Block\BlockUpdateStruct", but is of type "string".
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
