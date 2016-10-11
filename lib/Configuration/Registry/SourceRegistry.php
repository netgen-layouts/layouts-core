<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\Source\Source;
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
     * @param \Netgen\BlockManager\Configuration\Source\Source $source
     */
    public function addSource(Source $source)
    {
        $this->sources[$source->getIdentifier()] = $source;
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
     * @return \Netgen\BlockManager\Configuration\Source\Source
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
     * @return \Netgen\BlockManager\Configuration\Source\Source[]
     */
    public function getSources()
    {
        return $this->sources;
    }
}
