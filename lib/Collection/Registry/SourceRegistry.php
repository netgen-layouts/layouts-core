<?php

namespace Netgen\BlockManager\Collection\Registry;

use Netgen\BlockManager\Collection\Source\Source;
use Netgen\BlockManager\Exception\InvalidArgumentException;

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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If source with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Collection\Source\Source
     */
    public function getSource($identifier)
    {
        if (!$this->hasSource($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'Source with "%s" identifier does not exist.',
                    $identifier
                )
            );
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
}
