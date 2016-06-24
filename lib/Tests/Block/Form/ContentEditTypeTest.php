<?php

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Parameters\Form\ParametersType;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Block\Form\ContentEditType;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentEditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition
     */
    protected $blockDefinition;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $config = new Configuration(
            'block_definition',
            array(
                'content' => new Form('content', ContentEditType::class, array('css_class')),
            ),
            array(
                'large' => new ViewType(
                    'large',
                    'Large',
                    array(
                        'standard' => new ItemViewType('standard', 'Standard'),
                    )
                ),
                'small' => new ViewType(
                    'small',
                    'Small',
                    array(
                        'standard' => new ItemViewType('standard', 'Standard'),
                    )
                ),
            )
        );

        $this->blockDefinition = new BlockDefinition(
            'block_definition',
            new BlockDefinitionHandler(),
            $config
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new ContentEditType();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getTypes()
    {
        $formMapper = new FormMapper(
            new ParameterFilterRegistry(),
            array('text_line' => new TextLine())
        );

        return array(new ParametersType($formMapper));
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
        $updatedStruct->setParameter('css_class', 'Some CSS class');

        $form = $this->factory->create(
            ContentEditType::class,
            new BlockUpdateStruct(),
            array('blockDefinition' => $this->blockDefinition)
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }

        foreach (array_keys($submittedData['parameters']) as $key) {
            self::assertArrayHasKey($key, $children['parameters']);
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
                'blockDefinition' => $this->blockDefinition,
                'data' => new BlockUpdateStruct(),
            )
        );

        self::assertEquals($options['blockDefinition'], $this->blockDefinition);
        self::assertEquals($options['data'], new BlockUpdateStruct());
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
                'blockDefinition' => '',
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
                'blockDefinition' => $this->blockDefinition,
                'data' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('block_content_edit', $this->formType->getBlockPrefix());
    }
}
