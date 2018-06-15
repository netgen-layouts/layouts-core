<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Form;

use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Form\QueryEditType;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class QueryEditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface
     */
    private $queryType;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    private $query;

    public function setUp(): void
    {
        parent::setUp();

        $this->queryType = new QueryType('query_type');

        $this->query = new Query(['queryType' => $this->queryType, 'mainLocale' => 'en']);
    }

    public function getMainType(): FormTypeInterface
    {
        return new QueryEditType();
    }

    public function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    public function getTypes(): array
    {
        $formMapperRegistry = new FormMapperRegistry();
        $formMapperRegistry->addFormMapper('text_line', new Mapper\TextLineMapper());
        $formMapperRegistry->addFormMapper('integer', new Mapper\IntegerMapper());

        return [new ParametersType($formMapperRegistry)];
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'parameters' => [
                'param' => 'Param value',
            ],
        ];

        $updatedStruct = new QueryUpdateStruct(['locale' => 'en']);
        $updatedStruct->setParameterValue('param', 'Param value');

        $form = $this->factory->create(
            QueryEditType::class,
            new QueryUpdateStruct(['locale' => 'en']),
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
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildView
     */
    public function testDisableUntranslatableFormsOnNonMainLocale(): void
    {
        $form = $this->factory->create(
            QueryEditType::class,
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
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildView
     */
    public function testDisableUntranslatableFormsOnMainLocale(): void
    {
        $form = $this->factory->create(
            QueryEditType::class,
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
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::configureOptions
     */
    public function testConfigureOptions(): void
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
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "query" is missing.
     */
    public function testConfigureOptionsWithMissingQuery(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "query" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Collection\Query", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidQueryType(): void
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
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData(): void
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
