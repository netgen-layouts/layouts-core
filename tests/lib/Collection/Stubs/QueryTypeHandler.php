<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Stubs;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\TextLineType;

use function array_slice;
use function count;

final class QueryTypeHandler implements QueryTypeHandlerInterface
{
    /**
     * @var mixed[]
     */
    private array $values;

    private ?int $count;

    private bool $isContextual;

    /**
     * @param mixed[] $values
     */
    public function __construct(array $values = [], ?int $count = null, bool $isContextual = false)
    {
        $this->values = $values;
        $this->count = $count;
        $this->isContextual = $isContextual;
    }

    public function buildParameters(ParameterBuilderInterface $builder): void {}

    /**
     * @return array<string, \Netgen\Layouts\Parameters\ParameterDefinition>
     */
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
                ],
            ),
            'param2' => ParameterDefinition::fromArray(
                [
                    'name' => 'param2',
                    'type' => new TextLineType(),
                    'isRequired' => false,
                    'options' => [
                        'translatable' => true,
                    ],
                ],
            ),
        ];
    }

    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable
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
