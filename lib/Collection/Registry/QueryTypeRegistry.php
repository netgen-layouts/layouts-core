<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Registry;

use ArrayIterator;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;
use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use Netgen\BlockManager\Exception\RuntimeException;

final class QueryTypeRegistry implements QueryTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface[]
     */
    private $queryTypes;

    /**
     * @param \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface[] $queryTypes
     */
    public function __construct(array $queryTypes)
    {
        $this->queryTypes = array_filter(
            $queryTypes,
            function (QueryTypeInterface $queryType): bool {
                return true;
            }
        );
    }

    public function hasQueryType(string $type): bool
    {
        return isset($this->queryTypes[$type]);
    }

    public function getQueryType(string $type): QueryTypeInterface
    {
        if (!$this->hasQueryType($type)) {
            throw QueryTypeException::noQueryType($type);
        }

        return $this->queryTypes[$type];
    }

    public function getQueryTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->queryTypes;
        }

        return array_filter(
            $this->queryTypes,
            function (QueryTypeInterface $queryType): bool {
                return $queryType->isEnabled();
            }
        );
    }

    public function getIterator()
    {
        return new ArrayIterator($this->queryTypes);
    }

    public function count()
    {
        return count($this->queryTypes);
    }

    public function offsetExists($offset)
    {
        return $this->hasQueryType($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getQueryType($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
