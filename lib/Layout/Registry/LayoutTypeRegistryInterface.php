<?php

namespace Netgen\BlockManager\Layout\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;

interface LayoutTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a layout type to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Layout\Type\LayoutTypeInterface $layoutType
     */
    public function addLayoutType($identifier, LayoutTypeInterface $layoutType);

    /**
     * Returns if registry has a layout type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasLayoutType($identifier);

    /**
     * Returns the layout type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Layout\LayoutTypeException If layout type with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutTypeInterface
     */
    public function getLayoutType($identifier);

    /**
     * Returns all layout types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutTypeInterface[]
     */
    public function getLayoutTypes($onlyEnabled = false);
}
