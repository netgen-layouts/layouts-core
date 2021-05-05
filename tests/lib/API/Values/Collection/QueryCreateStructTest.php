<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\Collection\QueryType\QueryType;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class QueryCreateStructTest extends TestCase
{
    private QueryCreateStruct $struct;

    private QueryTypeInterface $queryType;

    protected function setUp(): void
    {
        $this->queryType = $this->buildQueryType();

        $this->struct = new QueryCreateStruct($this->queryType);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\QueryCreateStruct::__construct
     * @covers \Netgen\Layouts\API\Values\Collection\QueryCreateStruct::getQueryType
     */
    public function testGetQueryType(): void
    {
        $queryCreateStruct = new QueryCreateStruct($this->queryType);

        self::assertSame($this->queryType, $queryCreateStruct->getQueryType());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\QueryCreateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHash(): void
    {
        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($initialValues);

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
     * @covers \Netgen\Layouts\API\Values\Collection\QueryCreateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues(): void
    {
        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($initialValues);

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
