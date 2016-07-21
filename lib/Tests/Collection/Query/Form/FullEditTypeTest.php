<?php

namespace Netgen\BlockManager\Tests\Collection\Query\Form;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Parameters\Form\ParametersType;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Integer;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Query\Form\FullEditType;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FullEditTypeTest extends FormTestCase
{
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

        $config = new Configuration(
            'query_type',
            'Query type'
        );

        $this->queryType = new QueryType(
            'query_type',
            new QueryTypeHandler(),
            $config
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new FullEditType();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getTypes()
    {
        $formMapper = new FormMapper(
            new ParameterFilterRegistry(),
            array(
                'text_line' => new TextLine(),
                'integer' => new Integer(),
            )
        );

        return array(new ParametersType($formMapper));
    }

    /**
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
            $this->assertArrayHasKey($key, $children);
        }

        foreach (array_keys($submittedData['parameters']) as $key) {
            $this->assertArrayHasKey($key, $children['parameters']);
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

        $this->assertEquals($this->queryType, $options['queryType']);
        $this->assertEquals(new QueryUpdateStruct(), $options['data']);
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
}
