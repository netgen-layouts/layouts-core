<?php

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\Configuration\BlockDefinition\ViewType;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition as Configuration;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Block\Form\FullEditType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FullEditTypeTest extends TypeTestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface
     */
    protected $parameterFormMapper;

    /**
     * @var \Netgen\BlockManager\Block\Form\FullEditType
     */
    protected $formType;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->parameterFormMapper = new FormMapper();
        $this->parameterFormMapper->addParameterHandler('text', new Text());

        $this->formType = new FullEditType($this->parameterFormMapper);

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
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::__construct
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
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

        $updatedStruct = new BlockUpdateStruct();
        $updatedStruct->viewType = 'large';
        $updatedStruct->name = 'My block';
        $updatedStruct->setParameter('css_id', 'Some CSS ID');
        $updatedStruct->setParameter('css_class', 'Some CSS class');

        $blockDefinition = new BlockDefinition();
        $blockDefinition->setConfiguration(
            new Configuration(
                'block_definition',
                array(),
                array(
                    'large' => new ViewType('large', 'Large'),
                    'small' => new ViewType('small', 'Small'),
                )
            )
        );

        $form = $this->factory->create(
            'block_full_edit',
            new BlockUpdateStruct(),
            array('blockDefinition' => $blockDefinition)
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
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'blockDefinition' => new BlockDefinition(),
                'data' => new BlockUpdateStruct(),
            )
        );

        self::assertEquals($options['blockDefinition'], new BlockDefinition());
        self::assertEquals($options['data'], new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
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
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
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
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'blockDefinition' => new BlockDefinition(),
                'data' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::getName
     */
    public function testGetName()
    {
        self::assertEquals('block_full_edit', $this->formType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('block_full_edit', $this->formType->getBlockPrefix());
    }
}
