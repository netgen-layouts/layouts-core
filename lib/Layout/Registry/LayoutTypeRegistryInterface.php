<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;

interface LayoutTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a layout type to registry.
     */
    public function addLayoutType(string $identifier, LayoutTypeInterface $layoutType): void;

    /**
     * Returns if registry has a layout type.
     */
    public function hasLayoutType(string $identifier): bool;

    /**
     * Returns the layout type with provided identifier.
     *
     * @throws \Netgen\BlockManager\Exception\Layout\LayoutTypeException If layout type with provided identifier does not exist
     */
    public function getLayoutType(string $identifier): LayoutTypeInterface;

    /**
     * Returns all layout types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutTypeInterface[]
     */
    public function getLayoutTypes(bool $onlyEnabled = false): array;
}
