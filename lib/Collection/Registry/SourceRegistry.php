<?php

namespace Netgen\BlockManager\Collection\Registry;

use ArrayIterator;
use Netgen\BlockManager\Collection\Source\Source;
use Netgen\BlockManager\Exception\Collection\SourceException;
use Netgen\BlockManager\Exception\RuntimeException;

class SourceRegistry implements SourceRegistryInterface
{
    /**
     * @var array
     */
    protected $sources = array();

    /**
     * Adds a source to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Collection\Source\Source $source
     */
    public function addSource($identifier, Source $source)
    {
        $this->sources[$identifier] = $source;
    }

    /**
     * Returns if registry has a source.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasSource($identifier)
    {
        return isset($this->sources[$identifier]);
    }

    /**
     * Returns the source with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Collection\SourceException If source with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Collection\Source\Source
     */
    public function getSource($identifier)
    {
        if (!$this->hasSource($identifier)) {
            throw SourceException::noSource($identifier);
        }

        return $this->sources[$identifier];
    }

    /**
     * Returns all sources.
     *
     * @return \Netgen\BlockManager\Collection\Source\Source[]
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->sources);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->sources);
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
        return $this->hasSource($offset);
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
        return $this->getSource($offset);
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
