<?php

namespace Netgen\BlockManager\Value\Registry;

use Netgen\BlockManager\Value\ValueLoaderInterface;
use InvalidArgumentException;

class ValueLoaderRegistry implements ValueLoaderRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Value\ValueLoaderInterface[]
     */
    protected $valueLoaders = array();

    /**
     * Returns a value loader for provided value type.
     *
     * @param string $valueType
     *
     * @throws \InvalidArgumentException If value loader does not exist
     *
     * @return \Netgen\BlockManager\Value\ValueLoaderInterface
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
     * Adds a value loader to registry.
     *
     * @param \Netgen\BlockManager\Value\ValueLoaderInterface $valueLoader
     */
    public function addValueLoader(ValueLoaderInterface $valueLoader)
    {
        $this->valueLoaders[$valueLoader->getValueType()] = $valueLoader;
    }

    /**
     * Returns all value loaders.
     *
     * @return \Netgen\BlockManager\Value\ValueLoaderInterface[]
     */
    public function getValueLoaders()
    {
        return $this->valueLoaders;
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
}
