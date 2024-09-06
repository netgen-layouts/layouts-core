<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Form;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryUpdateStruct;
use Netgen\Layouts\Collection\Form\QueryEditType;
use Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

final class QueryEditTypeTest extends FormTestCase
{
    private QueryType $queryType;

    private Query $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queryType = new QueryType('query_type');

        $this->query = Query::fromArray(['queryType' => $this->queryType, 'mainLocale' => 'en']);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::buildForm
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::buildView
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
            ['query' => $this->query],
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
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::buildForm
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::buildView
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
                    ],
                ),
            ],
        );

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertTrue($form->get('parameters')->get('param')->isDisabled());
        self::assertFalse($form->get('parameters')->get('param2')->isDisabled());
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::buildForm
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::buildView
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
                    ],
                ),
            ],
        );

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertFalse($form->get('parameters')->get('param')->isDisabled());
        self::assertFalse($form->get('parameters')->get('param2')->isDisabled());
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::configureOptions
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
            ],
        );

        self::assertSame($this->query, $options['query']);
        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::configureOptions
     */
    public function testConfigureOptionsWithMissingQuery(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "query" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidQueryType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "query" with value "" is expected to be of type "Netgen\Layouts\API\Values\Collection\Query", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'query' => '',
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Collection\Form\QueryEditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\Collection\QueryUpdateStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'query' => $this->query,
                'data' => '',
            ],
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

        return [new ParametersType(new Container($formMappers))];
    }
}
