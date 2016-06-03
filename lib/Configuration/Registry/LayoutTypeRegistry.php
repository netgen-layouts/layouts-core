<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use InvalidArgumentException;

class LayoutTypeRegistry implements LayoutTypeRegistryInterface
{
    /**
     * @var array
     */
    protected $layoutTypes = array();

    /**
     * Adds a layout type to registry.
     *
     * @param \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType
     */
    public function addLayoutType(LayoutType $layoutType)
    {
        $this->layoutTypes[$layoutType->getIdentifier()] = $layoutType;
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
     * @throws \InvalidArgumentException If layout type with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    public function getLayoutType($identifier)
    {
        if (!$this->hasLayoutType($identifier)) {
            throw new InvalidArgumentException(sprintf('Layout type "%s" does not exist.', $identifier));
        }

        return $this->layoutTypes[$identifier];
    }

    /**
     * Returns all layout types.
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType[]
     */
    public function getLayoutTypes()
    {
        return $this->layoutTypes;
    }
}
