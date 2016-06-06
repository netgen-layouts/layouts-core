<?php

namespace Netgen\BlockManager\Item\Registry;

use Netgen\BlockManager\Item\ValueLoaderInterface;

interface ValueLoaderRegistryInterface
{
    /**
     * Adds a value loader to registry.
     *
     * @param \Netgen\BlockManager\Item\ValueLoaderInterface $valueLoader
     */
    public function addValueLoader(ValueLoaderInterface $valueLoader);

    /**
     * Returns if registry has a value loader.
     *
     * @param string $valueType
     *
     * @return bool
     */
    public function hasValueLoader($valueType);

    /**
     * Returns a value loader for provided value type.
     *
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If value loader does not exist
     *
     * @return \Netgen\BlockManager\Item\ValueLoaderInterface
     */
    public function getValueLoader($valueType);

    /**
     * Returns all value loaders.
     *
     * @return \Netgen\BlockManager\Item\ValueLoaderInterface[]
     */
    public function getValueLoaders();
}
