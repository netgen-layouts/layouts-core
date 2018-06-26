<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;

final class ConditionTypeRegistry implements ConditionTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface[]
     */
    private $conditionTypes = [];

    public function __construct(ConditionTypeInterface ...$conditionTypes)
    {
        foreach ($conditionTypes as $conditionType) {
            $this->conditionTypes[$conditionType->getType()] = $conditionType;
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

    public function getIterator()
    {
        return new ArrayIterator($this->conditionTypes);
    }

    public function count()
    {
        return count($this->conditionTypes);
    }

    public function offsetExists($offset)
    {
        return $this->hasConditionType($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getConditionType($offset);
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
