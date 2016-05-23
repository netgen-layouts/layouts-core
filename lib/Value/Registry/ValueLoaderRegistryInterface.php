<?php

namespace Netgen\BlockManager\Value\Registry;

use Netgen\BlockManager\Value\ValueLoaderInterface;

interface ValueLoaderRegistryInterface
{
    /**
     * Returns a value loader for provided value type.
     *
     * @param string $valueType
     *
     * @throws \InvalidArgumentException If value loader does not exist
     *
     * @return \Netgen\BlockManager\Value\ValueLoaderInterface
     */
    public function getValueLoader($valueType);

    /**
     * Adds a value loader to registry.
     *
     * @param \Netgen\BlockManager\Value\ValueLoaderInterface $valueLoader
     */
    public function addValueLoader(ValueLoaderInterface $valueLoader);

    /**
     * Returns all value loaders.
     *
     * @return \Netgen\BlockManager\Value\ValueLoaderInterface[]
     */
    public function getValueLoaders();

    /**
     * Returns if registry has a value loader.
     *
     * @param string $valueType
     *
     * @return bool
     */
    public function hasValueLoader($valueType);
}
