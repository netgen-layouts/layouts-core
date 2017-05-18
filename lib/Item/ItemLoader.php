<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\Item\ItemException;

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

    /**
     * Loads the item from provided value ID and value type.
     *
     * @param int|string $valueId
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If item could not be loaded
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function load($valueId, $valueType)
    {
        if (!isset($this->valueLoaders[$valueType])) {
            throw ItemException::noValueType($valueType);
        }

        return $this->itemBuilder->build(
            $this->valueLoaders[$valueType]->load($valueId)
        );
    }
}
