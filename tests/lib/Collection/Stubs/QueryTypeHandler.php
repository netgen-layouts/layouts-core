<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Stubs;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Tests\Stubs\ParameterBuilderTrait;

use function array_slice;
use function count;

final class QueryTypeHandler implements QueryTypeHandlerInterface
{
    use ParameterBuilderTrait;

    /**
     * @param mixed[] $values
     */
    public function __construct(
        private array $values = [],
        private ?int $count = null,
        private bool $isContextual = false,
    ) {}

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add(
            'param',
            ParameterType\TextLineType::class,
            [
                'required' => true,
                'default_value' => 'value',
                'translatable' => false,
            ],
        );

        $builder->add(
            'param2',
            ParameterType\TextLineType::class,
            [
                'translatable' => true,
            ],
        );
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
