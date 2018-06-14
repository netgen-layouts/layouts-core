<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\TargetTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;

final class TargetTypeRegistry implements TargetTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface[]
     */
    private $targetTypes = [];

    public function addTargetType(TargetTypeInterface $targetType): void
    {
        $this->targetTypes[$targetType->getType()] = $targetType;
    }

    public function hasTargetType(string $type): bool
    {
        return isset($this->targetTypes[$type]);
    }

    public function getTargetType(string $type): TargetTypeInterface
    {
        if (!$this->hasTargetType($type)) {
            throw TargetTypeException::noTargetType($type);
        }

        return $this->targetTypes[$type];
    }

    public function getTargetTypes(): array
    {
        return $this->targetTypes;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->targetTypes);
    }

    public function count()
    {
        return count($this->targetTypes);
    }

    public function offsetExists($offset)
    {
        return $this->hasTargetType($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getTargetType($offset);
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
