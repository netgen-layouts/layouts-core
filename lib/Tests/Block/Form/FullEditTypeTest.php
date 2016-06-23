<?php

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Parameters\Form\ParametersType;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Block\Form\FullEditType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;

class FullEditTypeTest extends FormIntegrationTestCase
{
    /**
     * @var \Symfony\Component\Form\FormBuilder
     */
    protected $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    /**
     * @var \Netgen\BlockManager\Block\Form\FullEditType
     */
    protected $formType;

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

        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);

        $this->formType = new FullEditType();

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->formType)
            ->addType(new ParametersType(new FormMapper(array('text_line' => new TextLine()))))
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new FormTypeValidatorExtension($validator))
            ->getFormFactory();

        $config = new Configuration(
            'block_definition',
            array(),
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
            'item_view_type' => 'standard',
            'name' => 'My block',
        );

        $updatedStruct = new BlockUpdateStruct();
        $updatedStruct->viewType = 'large';
        $updatedStruct->itemViewType = 'standard';
        $updatedStruct->name = 'My block';
        $updatedStruct->setParameter('css_id', 'Some CSS ID');
        $updatedStruct->setParameter('css_class', 'Some CSS class');

        $form = $this->factory->create(
            FullEditType::class,
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
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
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
                'blockDefinition' => $this->blockDefinition,
                'data' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('block_full_edit', $this->formType->getBlockPrefix());
    }
}
