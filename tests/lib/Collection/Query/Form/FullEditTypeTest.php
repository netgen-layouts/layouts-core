<?php

namespace Netgen\BlockManager\Tests\Collection\Query\Form;

use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Query\Form\FullEditType;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FullEditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    protected $query;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $queryType = new QueryType('query_type');

        $this->query = new Query(array('queryType' => $queryType));
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
        $formMapperRegistry = new FormMapperRegistry();
        $formMapperRegistry->addFormMapper('text_line', new Mapper\TextLineMapper());
        $formMapperRegistry->addFormMapper('integer', new Mapper\IntegerMapper());

        return array(new ParametersType($formMapperRegistry));
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
        $updatedStruct->setParameterValue('param', 'Param value');

        $form = $this->factory->create(
            FullEditType::class,
            new QueryUpdateStruct(),
            array('query' => $this->query)
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
                'query' => $this->query,
                'data' => new QueryUpdateStruct(),
            )
        );

        $this->assertEquals($this->query, $options['query']);
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
                'query' => '',
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
                'query' => $this->query,
                'data' => '',
            )
        );
    }
}
