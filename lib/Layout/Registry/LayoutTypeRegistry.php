<?php

namespace Netgen\BlockManager\Layout\Registry;

use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Layout\Type\LayoutType;

class LayoutTypeRegistry implements LayoutTypeRegistryInterface
{
    /**
     * @var array
     */
    protected $layoutTypes = array();

    /**
     * Adds a layout type to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     */
    public function addLayoutType($identifier, LayoutType $layoutType)
    {
        $this->layoutTypes[$identifier] = $layoutType;
    }

    /**
     * Returns if registry has a layout type.
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
     * Returns the layout type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Layout\LayoutTypeException If layout type with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutType
     */
    public function getLayoutType($identifier)
    {
        if (!$this->hasLayoutType($identifier)) {
            throw LayoutTypeException::noLayoutType($identifier);
        }

        return $this->layoutTypes[$identifier];
    }

    /**
     * Returns all layout types.
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutType[]
     */
    public function getLayoutTypes()
    {
        return $this->layoutTypes;
    }
}
