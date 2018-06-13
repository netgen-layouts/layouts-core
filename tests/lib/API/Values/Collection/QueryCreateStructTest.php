<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Collection\QueryType\QueryType;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class QueryCreateStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct
     */
    private $struct;

    public function setUp()
    {
        $this->struct = new QueryCreateStruct();
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct::fillParameters
     */
    public function testFillParameters()
    {
        $queryType = $this->buildQueryType();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParameters($queryType, $initialValues);

        $this->assertEquals(
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
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct::fillParameters
     */
    public function testFillParametersWithMissingValues()
    {
        $queryType = $this->buildQueryType();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParameters($queryType, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct::fillParametersFromQuery
     */
    public function testFillParametersFromQuery()
    {
        $queryType = $this->buildQueryType();

        /** @var \Netgen\BlockManager\Parameters\CompoundParameterDefinition $compoundDefinition */
        $compoundDefinition = $queryType->getParameterDefinition('compound');

        $query = new Query(
            [
                'queryType' => $queryType,
                'parameters' => [
                    'css_class' => new Parameter(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $queryType->getParameterDefinition('css_class'),
                        ]
                    ),
                    'inner' => new Parameter(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $compoundDefinition->getParameterDefinition('inner'),
                        ]
                    ),
                ],
            ]
        );

        $this->struct->fillParametersFromQuery($query);

        $this->assertEquals(
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
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHash()
    {
        $queryType = $this->buildQueryType();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($queryType, $initialValues);

        $this->assertEquals(
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
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues()
    {
        $queryType = $this->buildQueryType();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($queryType, $initialValues);

        $this->assertEquals(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @return \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface
     */
    private function buildQueryType()
    {
        $compoundParameter = new CompoundParameterDefinition(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => new ParameterDefinition(
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
            'css_class' => new ParameterDefinition(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css_default',
                ]
            ),
            'css_id' => new ParameterDefinition(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id_default',
                ]
            ),
            'compound' => $compoundParameter,
        ];

        return new QueryType(['parameterDefinitions' => $parameterDefinitions]);
    }
}
