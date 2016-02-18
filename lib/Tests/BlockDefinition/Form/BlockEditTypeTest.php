<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Form;

use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Tests\BlockDefinition\Stubs\BlockDefinition;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\BlockDefinition\Form\BlockEditType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BlockEditTypeTest extends TypeTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->blockDefinitionRegistry = $this->getMock(
            BlockDefinitionRegistryInterface::class
        );

        $this->blockDefinitionRegistry
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue(new BlockDefinition()));

        $this->configuration = $this->getMock(
            ConfigurationInterface::class
        );

        $blockConfig = array(
            'view_types' => array(
                'large' => array('name' => 'Large'),
                'small' => array('name' => 'Small'),
            ),
        );

        $this->configuration
            ->expects($this->any())
            ->method('getBlockConfig')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue($blockConfig));

        $validator = $this->getMock(ValidatorInterface::class);
        $validator
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType(
                new BlockEditType(
                    $this->blockDefinitionRegistry,
                    $this->configuration
                )
            )
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new FormTypeValidatorExtension($validator))
            ->getFormFactory();
    }

    /**
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::__construct
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'parameters' => array(
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
            'view_type' => 'large',
            'name' => 'My block',
        );

        $block = new Block(
            array(
                'definitionIdentifier' => 'block_definition',
            )
        );

        $blockUpdateStruct = new BlockUpdateStruct();

        $updatedStruct = new BlockUpdateStruct();
        $updatedStruct->viewType = 'large';
        $updatedStruct->name = 'My block';
        $updatedStruct->setParameter('css_id', 'Some CSS ID');
        $updatedStruct->setParameter('css_class', 'Some CSS class');

        $form = $this->factory->create(
            'block_edit',
            $blockUpdateStruct,
            array('block' => $block)
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
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::configureOptions
     */
    public function testConfigureOptions()
    {
        $formType = new BlockEditType(
            $this->blockDefinitionRegistry,
            $this->configuration
        );

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'block' => new Block(),
                'data' => new BlockUpdateStruct(),
            )
        );

        self::assertEquals($options['block'], new Block());
        self::assertEquals($options['data'], new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingBlock()
    {
        $formType = new BlockEditType(
            $this->blockDefinitionRegistry,
            $this->configuration
        );

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidBlock()
    {
        $formType = new BlockEditType(
            $this->blockDefinitionRegistry,
            $this->configuration
        );

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'block' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $formType = new BlockEditType(
            $this->blockDefinitionRegistry,
            $this->configuration
        );

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'block' => new Block(),
                'data' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::getName
     */
    public function testGetName()
    {
        $formType = new BlockEditType(
            $this->blockDefinitionRegistry,
            $this->configuration
        );

        self::assertEquals('block_edit', $formType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Form\Type\UpdateBlockType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $formType = new BlockEditType(
            $this->blockDefinitionRegistry,
            $this->configuration
        );

        self::assertEquals('block_edit', $formType->getBlockPrefix());
    }
}
