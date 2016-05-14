<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\Source\Source;
use RuntimeException;

class SourceRegistry
{
    protected $sources = array();

    /**
     * Adds a source.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Configuration\Source\Source $source
     */
    public function addSource($identifier, Source $source)
    {
        $this->sources[$identifier] = $source;
    }

    /**
     * Returns if source exists in the registry.
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
     * Returns the source.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If source with provided identifier does not exist.
     *
     * @return \Netgen\BlockManager\Configuration\Source\Source
     */
    public function getSource($identifier)
    {
        if (!$this->hasSource($identifier)) {
            throw new RuntimeException(sprintf('Source "%s" does not exist.', $identifier));
        }

        return $this->sources[$identifier];
    }

    /**
     * Returns all sources.
     *
     * @return \Netgen\BlockManager\Configuration\Source\Source[]
     */
    public function all()
    {
        return $this->sources;
    }
}
