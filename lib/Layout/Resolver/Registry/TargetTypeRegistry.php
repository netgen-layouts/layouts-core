<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\TargetTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Traversable;

final class TargetTypeRegistry implements TargetTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface[]
     */
    private $targetTypes = [];

    public function __construct(iterable $targetTypes)
    {
        foreach ($targetTypes as $targetType) {
            if ($targetType instanceof TargetTypeInterface) {
                $this->targetTypes[$targetType::getType()] = $targetType;
            }
        }
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

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->targetTypes);
    }

    public function count(): int
    {
        return count($this->targetTypes);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasTargetType($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getTargetType($offset);
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
