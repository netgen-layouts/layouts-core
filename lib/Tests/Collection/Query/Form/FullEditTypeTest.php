<?php

namespace Netgen\BlockManager\Tests\Collection\Query\Form;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Core\Values\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Query\Form\FullEditType;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;
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
     * @var \Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface
     */
    protected $parameterFormMapper;

    /**
     * @var \Netgen\BlockManager\Collection\Query\Form\FullEditType
     */
    protected $formType;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType
     */
    protected $queryType;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);

        $this->parameterFormMapper = new FormMapper(array('text_line' => new TextLine()));
        $this->formType = new FullEditType($this->parameterFormMapper);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->formType)
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new FormTypeValidatorExtension($validator))
            ->getFormFactory();

        $config = new Configuration(
            'query_type',
            'Query type',
            array(),
            array()
        );

        $this->queryType = new QueryType(
            'query_type',
            new QueryTypeHandler(),
            $config
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::__construct
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'parameters' => array(
                'param' => 'Param value',
            ),
        );

        $updatedStruct = new QueryUpdateStruct();
        $updatedStruct->setParameter('param', 'Param value');

        $form = $this->factory->create(
            FullEditType::class,
            new QueryUpdateStruct(),
            array('queryType' => $this->queryType)
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
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'queryType' => $this->queryType,
                'data' => new QueryUpdateStruct(),
            )
        );

        self::assertEquals($options['queryType'], $this->queryType);
        self::assertEquals($options['data'], new QueryUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingQueryType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidQueryType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'queryType' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'queryType' => $this->queryType,
                'data' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('query_full_edit', $this->formType->getBlockPrefix());
    }
}
