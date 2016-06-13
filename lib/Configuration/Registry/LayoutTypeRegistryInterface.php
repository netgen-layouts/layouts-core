<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;

interface LayoutTypeRegistryInterface
{
    /**
     * Adds a layout type to registry.
     *
     * @param \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType
     */
    public function addLayoutType(LayoutType $layoutType);

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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If layout type with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    public function getLayoutType($identifier);

    /**
     * Returns all layout types.
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType[]
     */
    public function getLayoutTypes();
}
