<?php

namespace Netgen\BlockManager\Collection\Registry;

use ArrayIterator;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use Netgen\BlockManager\Exception\RuntimeException;

final class QueryTypeRegistry implements QueryTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface[]
     */
    private $queryTypes = [];

    public function addQueryType($type, QueryTypeInterface $queryType)
    {
        $this->queryTypes[$type] = $queryType;
    }

    public function hasQueryType($type)
    {
        return isset($this->queryTypes[$type]);
    }

    public function getQueryType($type)
    {
        if (!$this->hasQueryType($type)) {
            throw QueryTypeException::noQueryType($type);
        }

        return $this->queryTypes[$type];
    }

    public function getQueryTypes($onlyEnabled = false)
    {
        if (!$onlyEnabled) {
            return $this->queryTypes;
        }

        return array_filter(
            $this->queryTypes,
            function (QueryTypeInterface $queryType) {
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
