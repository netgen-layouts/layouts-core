<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

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
    public function __construct(ItemBuilderInterface $itemBuilder, array $valueLoaders = [])
    {
        $this->itemBuilder = $itemBuilder;

        $this->valueLoaders = array_filter(
            $valueLoaders,
            function (ValueLoaderInterface $valueLoader) {
                return true;
            }
        );
    }

    public function load($value, $valueType)
    {
        if (!isset($this->valueLoaders[$valueType])) {
            throw ItemException::noValueType($valueType);
        }

        try {
            $loadedValue = $this->valueLoaders[$valueType]->load($value);
        } catch (ItemException $e) {
            return new NullItem($valueType);
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
            return new NullItem($valueType);
        }

        return $this->itemBuilder->build($loadedValue);
    }
}
