<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\Source\Source;

interface SourceRegistryInterface
{
    /**
     * Adds a source.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Configuration\Source\Source $source
     */
    public function addSource($identifier, Source $source);

    /**
     * Returns if source exists in the registry.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasSource($identifier);

    /**
     * Returns the source.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If source with provided identifier does not exist.
     *
     * @return \Netgen\BlockManager\Configuration\Source\Source
     */
    public function getSource($identifier);

    /**
     * Returns all sources.
     *
     * @return \Netgen\BlockManager\Configuration\Source\Source[]
     */
    public function all();
}
