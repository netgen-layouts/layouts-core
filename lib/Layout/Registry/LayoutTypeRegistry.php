<?php

namespace Netgen\BlockManager\Layout\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
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
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutType[]
     */
    public function getLayoutTypes($onlyEnabled = false)
    {
        if (!$onlyEnabled) {
            return $this->layoutTypes;
        }

        return array_filter(
            $this->layoutTypes,
            function (LayoutType $layoutType) {
                return $layoutType->isEnabled();
            }
        );
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->layoutTypes);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->layoutTypes);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasLayoutType($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getLayoutType($offset);
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
