<?php

namespace Netgen\BlockManager\Item\Registry;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Item\ValueLoaderInterface;

class ValueLoaderRegistry implements ValueLoaderRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ValueLoaderInterface[]
     */
    protected $valueLoaders = array();

    /**
     * Adds a value loader to registry.
     *
     * @param string $valueType
     * @param \Netgen\BlockManager\Item\ValueLoaderInterface $valueLoader
     */
    public function addValueLoader($valueType, ValueLoaderInterface $valueLoader)
    {
        $this->valueLoaders[$valueType] = $valueLoader;
    }

    /**
     * Returns if registry has a value loader.
     *
     * @param string $valueType
     *
     * @return bool
     */
    public function hasValueLoader($valueType)
    {
        return isset($this->valueLoaders[$valueType]);
    }

    /**
     * Returns a value loader for provided value type.
     *
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If value loader does not exist
     *
     * @return \Netgen\BlockManager\Item\ValueLoaderInterface
     */
    public function getValueLoader($valueType)
    {
        if (!$this->hasValueLoader($valueType)) {
            throw new InvalidArgumentException(
                'valueType',
                sprintf(
                    'Value loader for "%s" value type does not exist.',
                    $valueType
                )
            );
        }

        return $this->valueLoaders[$valueType];
    }

    /**
     * Returns all value loaders.
     *
     * @return \Netgen\BlockManager\Item\ValueLoaderInterface[]
     */
    public function getValueLoaders()
    {
        return $this->valueLoaders;
    }
}
