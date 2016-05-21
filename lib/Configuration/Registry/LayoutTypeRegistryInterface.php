<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use RuntimeException;

interface LayoutTypeRegistryInterface
{
    /**
     * Adds a layout type.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType
     */
    public function addLayoutType($identifier, LayoutType $layoutType);
    /**
     * Returns if layout type exists in the registry.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasLayoutType($identifier);

    /**
     * Returns the layout type.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If layout type with provided identifier does not exist.
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    public function getLayoutType($identifier);

    /**
     * Returns all layout types.
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType[]
     */
    public function all();
}
