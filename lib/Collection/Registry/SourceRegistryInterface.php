<?php

namespace Netgen\BlockManager\Collection\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Collection\Source\Source;

interface SourceRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a source to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Collection\Source\Source $source
     */
    public function addSource($identifier, Source $source);

    /**
     * Returns if registry has a source.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasSource($identifier);

    /**
     * Returns the source with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Collection\SourceException If source with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Collection\Source\Source
     */
    public function getSource($identifier);

    /**
     * Returns all sources.
     *
     * @return \Netgen\BlockManager\Collection\Source\Source[]
     */
    public function getSources();
}
