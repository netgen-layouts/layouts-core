<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface;
use RuntimeException;

class ItemBuilder implements ItemBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * @var \Netgen\BlockManager\Item\ValueConverterInterface[]
     */
    protected $valueConverters = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     * @param \Netgen\BlockManager\Item\ValueConverterInterface[] $valueConverters
     */
    public function __construct(
        ValueLoaderRegistryInterface $valueLoaderRegistry,
        array $valueConverters = array()
    ) {
        $this->valueLoaderRegistry = $valueLoaderRegistry;
        $this->valueConverters = $valueConverters;
    }

    /**
     * Builds the item from provided object.
     *
     * @param mixed $object
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Item\Item
     */
    public function buildFromObject($object)
    {
        foreach ($this->valueConverters as $valueConverter) {
            if (!$valueConverter instanceof ValueConverterInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Value converter for "%s" object needs to implement ValueConverterInterface.',
                        get_class($object)
                    )
                );
            }

            if (!$valueConverter->supports($object)) {
                continue;
            }

            $value = new Item(
                array(
                    'valueId' => $valueConverter->getId($object),
                    'valueType' => $valueConverter->getValueType($object),
                    'name' => $valueConverter->getName($object),
                    'isVisible' => $valueConverter->getIsVisible($object),
                    'object' => $object,
                )
            );

            return $value;
        }

        throw new RuntimeException(
            sprintf(
                'Value converter for object of type "%s" does not exist.',
                get_class($object)
            )
        );
    }

    /**
     * Builds the item from provided value ID and value type.
     *
     * @param int|string $valueId
     * @param string $valueType
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Item\Item
     */
    public function build($valueId, $valueType)
    {
        $valueLoader = $this->valueLoaderRegistry
            ->getValueLoader($valueType);

        $loadedValue = $valueLoader->load($valueId);

        return $this->buildFromObject($loadedValue);
    }
}
