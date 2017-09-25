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
