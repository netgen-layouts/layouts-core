<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;

final class QueryTypeHandler implements QueryTypeHandlerInterface
{
    /**
     * @var array
     */
    private $values;

    /**
     * @var int|null
     */
    private $count;

    /**
     * @var bool
     */
    private $isContextual;

    public function __construct(array $values = [], ?int $count = null, bool $isContextual = false)
    {
        $this->values = $values;
        $this->count = $count;
        $this->isContextual = $isContextual;
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
    }

    public function getParameterDefinitions(): array
    {
        return [
            'param' => ParameterDefinition::fromArray(
                [
                    'name' => 'param',
                    'type' => new TextLineType(),
                    'isRequired' => true,
                    'defaultValue' => 'value',
                    'options' => [
                        'translatable' => false,
                    ],
                ]
            ),
            'param2' => ParameterDefinition::fromArray(
                [
                    'name' => 'param2',
                    'type' => new TextLineType(),
                    'options' => [
                        'translatable' => true,
                    ],
                ]
            ),
        ];
    }

    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return array_slice($this->values, $offset, $limit);
    }

    public function getCount(Query $query): int
    {
        return $this->count ?? count($this->values);
    }

    public function isContextual(Query $query): bool
    {
        return $this->isContextual;
    }
}
