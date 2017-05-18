<?php

namespace Netgen\BlockManager\Layout\Registry;

use Netgen\BlockManager\Layout\Type\LayoutType;

interface LayoutTypeRegistryInterface
{
    /**
     * Adds a layout type to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     */
    public function addLayoutType($identifier, LayoutType $layoutType);

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
     * @return \Netgen\BlockManager\Layout\Type\LayoutType
     */
    public function getLayoutType($identifier);

    /**
     * Returns all layout types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutType[]
     */
    public function getLayoutTypes($onlyEnabled = false);
}
