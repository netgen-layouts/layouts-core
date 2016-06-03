<?php

namespace Netgen\BlockManager\Item\Registry;

use Netgen\BlockManager\Item\ValueLoaderInterface;
use InvalidArgumentException;

class ValueLoaderRegistry implements ValueLoaderRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ValueLoaderInterface[]
     */
    protected $valueLoaders = array();

    /**
     * Adds a value loader to registry.
     *
     * @param \Netgen\BlockManager\Item\ValueLoaderInterface $valueLoader
     */
    public function addValueLoader(ValueLoaderInterface $valueLoader)
    {
        $this->valueLoaders[$valueLoader->getValueType()] = $valueLoader;
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
     * @throws \InvalidArgumentException If value loader does not exist
     *
     * @return \Netgen\BlockManager\Item\ValueLoaderInterface
     */
    public function getValueLoader($valueType)
    {
        if (!$this->hasValueLoader($valueType)) {
            throw new InvalidArgumentException(
                'Value loader for "' . $valueType . '" value type does not exist.'
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
