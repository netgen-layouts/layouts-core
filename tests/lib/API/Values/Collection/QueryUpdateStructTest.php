<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Collection\QueryUpdateStruct;
use Netgen\Layouts\Collection\QueryType\QueryType;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class QueryUpdateStructTest extends TestCase
{
    private QueryUpdateStruct $struct;

    protected function setUp(): void
    {
        $this->struct = new QueryUpdateStruct();
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\QueryUpdateStruct::fillParametersFromQuery
     */
    public function testFillParametersFromQuery(): void
    {
        $queryType = $this->buildQueryType();

        /** @var \Netgen\Layouts\Parameters\CompoundParameterDefinition $compoundDefinition */
        $compoundDefinition = $queryType->getParameterDefinition('compound');

        $query = Query::fromArray(
            [
                'queryType' => $queryType,
                'parameters' => [
                    'css_class' => Parameter::fromArray(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $queryType->getParameterDefinition('css_class'),
                        ],
                    ),
                    'inner' => Parameter::fromArray(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $compoundDefinition->getParameterDefinition('inner'),
                        ],
                    ),
                ],
            ],
        );

        $this->struct->fillParametersFromQuery($query);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\QueryUpdateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHash(): void
    {
        $queryType = $this->buildQueryType();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($queryType, $initialValues);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\QueryUpdateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues(): void
    {
        $queryType = $this->buildQueryType();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($queryType, $initialValues);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    private function buildQueryType(): QueryTypeInterface
    {
        $compoundParameter = CompoundParameterDefinition::fromArray(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'isRequired' => false,
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => ParameterDefinition::fromArray(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'isRequired' => false,
                            'defaultValue' => 'inner_default',
                        ],
                    ),
                ],
            ],
        );

        $parameterDefinitions = [
            'css_class' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => false,
                    'defaultValue' => 'css_default',
                ],
            ),
            'css_id' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => false,
                    'defaultValue' => 'id_default',
                ],
            ),
            'compound' => $compoundParameter,
        ];

        return QueryType::fromArray(['parameterDefinitions' => $parameterDefinitions]);
    }
}
