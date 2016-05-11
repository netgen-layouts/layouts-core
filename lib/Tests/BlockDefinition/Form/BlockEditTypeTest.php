<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Form;

use Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface;
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
    protected $parameterFormMapperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockDefinitionRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationMock;

    /**
     * @var \Netgen\BlockManager\BlockDefinition\Form\BlockEditType
     */
    protected $formType;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->parameterFormMapperMock = $this->getMock(
            FormMapperInterface::class
        );

        $this->blockDefinitionRegistryMock = $this->getMock(
            BlockDefinitionRegistryInterface::class
        );

        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue(new BlockDefinition()));

        $this->configurationMock = $this->getMock(
            ConfigurationInterface::class
        );

        $blockDefinitionConfig = array(
            'view_types' => array(
                'large' => array('name' => 'Large'),
                'small' => array('name' => 'Small'),
            ),
        );

        $this->configurationMock
            ->expects($this->any())
            ->method('getBlockDefinitionConfig')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue($blockDefinitionConfig));

        $this->formType = new BlockEditType(
            $this->parameterFormMapperMock,
            $this->blockDefinitionRegistryMock,
            $this->configurationMock
        );

        $validator = $this->getMock(ValidatorInterface::class);
        $validator
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->formType)
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new FormTypeValidatorExtension($validator))
            ->getFormFactory();
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\BlockEditType::__construct
     * @covers \Netgen\BlockManager\BlockDefinition\Form\BlockEditType::buildForm
     */
    public function testSubmitValidData()
    {
        $this->markTestIncomplete('Not yet working');

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
     * @covers \Netgen\BlockManager\BlockDefinition\Form\BlockEditType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

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
     * @covers \Netgen\BlockManager\BlockDefinition\Form\BlockEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingBlock()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\BlockEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
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
     * @covers \Netgen\BlockManager\BlockDefinition\Form\BlockEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'block' => new Block(),
                'data' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\BlockEditType::getName
     */
    public function testGetName()
    {
        self::assertEquals('block_edit', $this->formType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\BlockEditType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('block_edit', $this->formType->getBlockPrefix());
    }
}
