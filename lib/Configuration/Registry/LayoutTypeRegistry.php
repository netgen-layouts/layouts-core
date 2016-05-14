<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use RuntimeException;

class LayoutTypeRegistry
{
    protected $layoutTypes = array();

    /**
     * Adds a layout type.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType
     */
    public function addLayoutType($identifier, LayoutType $layoutType)
    {
        $this->layoutTypes[$identifier] = $layoutType;
    }

    /**
     * Returns if layout type exists in the registry.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasLayoutType($identifier)
    {
        return isset($this->layoutTypes[$identifier]);
    }

    /**
     * Returns the layout type.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If layout type with provided identifier does not exist.
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    public function getLayoutType($identifier)
    {
        if (!$this->hasLayoutType($identifier)) {
            throw new RuntimeException(sprintf('Layout type "%s" does not exist.', $identifier));
        }

        return $this->layoutTypes[$identifier];
    }

    /**
     * Returns all layout types.
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType[]
     */
    public function all()
    {
        return $this->layoutTypes;
    }
}
