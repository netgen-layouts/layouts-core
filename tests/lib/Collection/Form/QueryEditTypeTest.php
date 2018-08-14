<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Form;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Form\QueryEditType;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
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

        $this->query = Query::fromArray(['queryType' => $this->queryType, 'mainLocale' => 'en']);
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

        $struct = new QueryUpdateStruct();
        $struct->locale = 'en';

        $form = $this->factory->create(
            QueryEditType::class,
            $struct,
            ['query' => $this->query]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame(['param' => 'Param value'], $struct->getParameterValues());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }

        foreach (array_keys($submittedData['parameters']) as $key) {
            self::assertArrayHasKey($key, $children['parameters']);
        }

        self::assertArrayHasKey('query', $view->vars);
        self::assertSame($this->query, $view->vars['query']);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildView
     */
    public function testDisableUntranslatableFormsOnNonMainLocale(): void
    {
        $struct = new QueryUpdateStruct();
        $struct->locale = 'hr';

        $form = $this->factory->create(
            QueryEditType::class,
            $struct,
            [
                'query' => Query::fromArray(
                    [
                        'queryType' => $this->queryType,
                        'mainLocale' => 'en',
                    ]
                ),
            ]
        );

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertTrue($form->get('parameters')->get('param')->isDisabled());
        self::assertFalse($form->get('parameters')->get('param2')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildForm
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::buildView
     */
    public function testDisableUntranslatableFormsOnMainLocale(): void
    {
        $struct = new QueryUpdateStruct();
        $struct->locale = 'en';

        $form = $this->factory->create(
            QueryEditType::class,
            $struct,
            [
                'query' => Query::fromArray(
                    [
                        'queryType' => $this->queryType,
                        'mainLocale' => 'en',
                    ]
                ),
            ]
        );

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertFalse($form->get('parameters')->get('param')->isDisabled());
        self::assertFalse($form->get('parameters')->get('param2')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Form\QueryEditType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new QueryUpdateStruct();

        $options = $optionsResolver->resolve(
            [
                'query' => $this->query,
                'data' => $struct,
            ]
        );

        self::assertSame($this->query, $options['query']);
        self::assertSame($struct, $options['data']);
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

    protected function getMainType(): FormTypeInterface
    {
        return new QueryEditType();
    }

    protected function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    protected function getTypes(): array
    {
        $formMappers = [
            'text_line' => new Mapper\TextLineMapper(),
            'integer' => new Mapper\IntegerMapper(),
        ];

        return [new ParametersType($formMappers)];
    }
}
