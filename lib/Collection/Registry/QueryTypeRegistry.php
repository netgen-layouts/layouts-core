<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Registry;

use ArrayIterator;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;
use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Traversable;

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
            static function (QueryTypeInterface $queryType): bool {
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
            static function (QueryTypeInterface $queryType): bool {
                return $queryType->isEnabled();
            }
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->queryTypes);
    }

    public function count(): int
    {
        return count($this->queryTypes);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasQueryType($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getQueryType($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
