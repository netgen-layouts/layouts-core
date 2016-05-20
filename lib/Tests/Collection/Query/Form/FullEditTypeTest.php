<?php

namespace Netgen\BlockManager\Tests\Collection\Query\Form;

use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Configuration\QueryType\QueryType as Configuration;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Core\Values\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Query\Form\FullEditType;
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
     * @var \Netgen\BlockManager\Collection\Query\Form\FullEditType
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
     * @covers \Netgen\BlockManager\Collection\Query\Form\EditType::__construct
     * @covers \Netgen\BlockManager\Collection\Query\Form\EditType::buildForm
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

        $queryType = new QueryType();
        $queryType->setConfig(
            new Configuration(
                'query_type',
                array()
            )
        );

        $form = $this->factory->create(
            'query_full_edit',
            new QueryUpdateStruct(),
            array('queryType' => $queryType)
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
     * @covers \Netgen\BlockManager\Collection\Query\Form\EditType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'queryType' => new QueryType(),
                'data' => new QueryUpdateStruct(),
            )
        );

        self::assertEquals($options['queryType'], new QueryType());
        self::assertEquals($options['data'], new QueryUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\EditType::configureOptions
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
     * @covers \Netgen\BlockManager\Collection\Query\Form\EditType::configureOptions
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
     * @covers \Netgen\BlockManager\Collection\Query\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'queryType' => new QueryType(),
                'data' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\EditType::getName
     */
    public function testGetName()
    {
        self::assertEquals('query_full_edit', $this->formType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\EditType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('query_full_edit', $this->formType->getBlockPrefix());
    }
}
