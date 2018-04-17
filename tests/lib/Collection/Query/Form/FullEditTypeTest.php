<?php

namespace Netgen\BlockManager\Tests\Collection\Query\Form;

use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Query\Form\FullEditType;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FullEditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    private $queryType;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    private $query;

    public function setUp()
    {
        parent::setUp();

        $this->queryType = new QueryType('query_type');

        $this->query = new Query(['queryType' => $this->queryType]);
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new FullEditType();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeExtensionInterface[]
     */
    public function getTypeExtensions()
    {
        return [new ParametersTypeExtension()];
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getTypes()
    {
        $formMapperRegistry = new FormMapperRegistry();
        $formMapperRegistry->addFormMapper('text_line', new Mapper\TextLineMapper());
        $formMapperRegistry->addFormMapper('integer', new Mapper\IntegerMapper());

        return [new ParametersType($formMapperRegistry)];
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'parameters' => [
                'param' => 'Param value',
            ],
        ];

        $updatedStruct = new QueryUpdateStruct();
        $updatedStruct->setParameterValue('param', 'Param value');

        $form = $this->factory->create(
            FullEditType::class,
            new QueryUpdateStruct(),
            ['query' => $this->query]
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

        $this->assertArrayHasKey('query', $view->vars);
        $this->assertEquals($this->query, $view->vars['query']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::buildView
     */
    public function testDisableUntranslatableFormsOnNonMainLocale()
    {
        $form = $this->factory->create(
            FullEditType::class,
            new QueryUpdateStruct(['locale' => 'hr']),
            [
                'query' => new Query(
                    [
                        'queryType' => $this->queryType,
                        'mainLocale' => 'en',
                    ]
                ),
            ]
        );

        $this->assertFalse($form->get('parameters')->isDisabled());
        $this->assertTrue($form->get('parameters')->get('param')->isDisabled());
        $this->assertFalse($form->get('parameters')->get('param2')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::buildView
     */
    public function testDisableUntranslatableFormsOnMainLocale()
    {
        $form = $this->factory->create(
            FullEditType::class,
            new QueryUpdateStruct(['locale' => 'en']),
            [
                'query' => new Query(
                    [
                        'queryType' => $this->queryType,
                        'mainLocale' => 'en',
                    ]
                ),
            ]
        );

        $this->assertFalse($form->get('parameters')->isDisabled());
        $this->assertFalse($form->get('parameters')->get('param')->isDisabled());
        $this->assertFalse($form->get('parameters')->get('param2')->isDisabled());
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
            [
                'query' => $this->query,
                'data' => new QueryUpdateStruct(),
            ]
        );

        $this->assertEquals($this->query, $options['query']);
        $this->assertEquals(new QueryUpdateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "query" is missing.
     */
    public function testConfigureOptionsWithMissingQuery()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "query" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Collection\Query", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidQueryType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'query' => '',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Query\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'query' => $this->query,
                'data' => '',
            ]
        );
    }
}
