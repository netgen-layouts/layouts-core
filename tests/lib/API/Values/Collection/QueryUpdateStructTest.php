<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryType\QueryType;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class QueryUpdateStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct
     */
    private $struct;

    public function setUp(): void
    {
        $this->struct = new QueryUpdateStruct();
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct::fillParametersFromQuery
     */
    public function testFillParametersFromQuery(): void
    {
        $queryType = $this->buildQueryType();

        /** @var \Netgen\BlockManager\Parameters\CompoundParameterDefinition $compoundDefinition */
        $compoundDefinition = $queryType->getParameterDefinition('compound');

        $query = Query::fromArray(
            [
                'queryType' => $queryType,
                'parameters' => [
                    'css_class' => Parameter::fromArray(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $queryType->getParameterDefinition('css_class'),
                        ]
                    ),
                    'inner' => Parameter::fromArray(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $compoundDefinition->getParameterDefinition('inner'),
                        ]
                    ),
                ],
            ]
        );

        $this->struct->fillParametersFromQuery($query);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct::fillParametersFromHash
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
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct::fillParametersFromHash
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
            $this->struct->getParameterValues()
        );
    }

    private function buildQueryType(): QueryTypeInterface
    {
        $compoundParameter = CompoundParameterDefinition::fromArray(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => ParameterDefinition::fromArray(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'defaultValue' => 'inner_default',
                        ]
                    ),
                ],
            ]
        );

        $parameterDefinitions = [
            'css_class' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css_default',
                ]
            ),
            'css_id' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id_default',
                ]
            ),
            'compound' => $compoundParameter,
        ];

        return QueryType::fromArray(['parameterDefinitions' => $parameterDefinitions]);
    }
}
