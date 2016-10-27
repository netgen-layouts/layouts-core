<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\InvalidItemException;
use Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface;

class ItemLoader implements ItemLoaderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     * @param \Netgen\BlockManager\Item\ItemBuilderInterface $itemBuilder
     */
    public function __construct(
        ValueLoaderRegistryInterface $valueLoaderRegistry,
        ItemBuilderInterface $itemBuilder
    ) {
        $this->valueLoaderRegistry = $valueLoaderRegistry;
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Loads the item from provided value ID and value type.
     *
     * @param int|string $valueId
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\InvalidItemException If item could not be loaded
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function load($valueId, $valueType)
    {
        if (!$this->valueLoaderRegistry->hasValueLoader($valueType)) {
            throw new InvalidItemException(
                sprintf('Value type "%s" does not exist.', $valueType)
            );
        }

        $valueLoader = $this->valueLoaderRegistry->getValueLoader($valueType);

        return $this->itemBuilder->build(
            $valueLoader->load($valueId)
        );
    }
}
