<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\InvalidItemException;

class ItemLoader implements ItemLoaderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * @var \Netgen\BlockManager\Item\ValueLoaderInterface[]
     */
    protected $valueLoaders;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ItemBuilderInterface $itemBuilder
     * @param \Netgen\BlockManager\Item\ValueLoaderInterface[] $valueLoaders
     */
    public function __construct(
        ItemBuilderInterface $itemBuilder,
        array $valueLoaders = array()
    ) {
        $this->itemBuilder = $itemBuilder;
        $this->valueLoaders = $valueLoaders;
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
        if (!isset($this->valueLoaders[$valueType])) {
            throw new InvalidItemException(
                sprintf('Value type "%s" does not exist.', $valueType)
            );
        }

        return $this->itemBuilder->build(
            $this->valueLoaders[$valueType]->load($valueId)
        );
    }
}
