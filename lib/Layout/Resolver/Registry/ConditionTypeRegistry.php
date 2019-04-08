<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Traversable;

final class ConditionTypeRegistry implements ConditionTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface[]
     */
    private $conditionTypes = [];

    public function __construct(iterable $conditionTypes)
    {
        foreach ($conditionTypes as $conditionType) {
            if ($conditionType instanceof ConditionTypeInterface) {
                $this->conditionTypes[$conditionType::getType()] = $conditionType;
            }
        }
    }

    public function hasConditionType(string $type): bool
    {
        return isset($this->conditionTypes[$type]);
    }

    public function getConditionType(string $type): ConditionTypeInterface
    {
        if (!$this->hasConditionType($type)) {
            throw ConditionTypeException::noConditionType($type);
        }

        return $this->conditionTypes[$type];
    }

    public function getConditionTypes(): array
    {
        return $this->conditionTypes;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->conditionTypes);
    }

    public function count(): int
    {
        return count($this->conditionTypes);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasConditionType($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getConditionType($offset);
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
