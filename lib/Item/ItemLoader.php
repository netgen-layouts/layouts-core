<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\Item\ItemException;

final class ItemLoader implements ItemLoaderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    private $itemBuilder;

    /**
     * @var \Netgen\BlockManager\Item\ValueLoaderInterface[]
     */
    private $valueLoaders;

    /**
     * @param \Netgen\BlockManager\Item\ItemBuilderInterface $itemBuilder
     * @param \Netgen\BlockManager\Item\ValueLoaderInterface[] $valueLoaders
     */
    public function __construct(
        ItemBuilderInterface $itemBuilder,
        array $valueLoaders = array()
    ) {
        foreach ($valueLoaders as $valueLoader) {
            if (!$valueLoader instanceof ValueLoaderInterface) {
                throw new InvalidInterfaceException(
                    'Value loader',
                    get_class($valueLoader),
                    ValueLoaderInterface::class
                );
            }
        }

        $this->itemBuilder = $itemBuilder;
        $this->valueLoaders = $valueLoaders;
    }

    public function load($value, $valueType)
    {
        if (!isset($this->valueLoaders[$valueType])) {
            throw ItemException::noValueType($valueType);
        }

        try {
            $loadedValue = $this->valueLoaders[$valueType]->load($value);
        } catch (ItemException $e) {
            return new NullItem($value);
        }

        return $this->itemBuilder->build($loadedValue);
    }

    public function loadByRemoteId($remoteId, $valueType)
    {
        if (!isset($this->valueLoaders[$valueType])) {
            throw ItemException::noValueType($valueType);
        }

        try {
            $loadedValue = $this->valueLoaders[$valueType]->loadByRemoteId($remoteId);
        } catch (ItemException $e) {
            return new NullItem($remoteId);
        }

        return $this->itemBuilder->build($loadedValue);
    }
}
