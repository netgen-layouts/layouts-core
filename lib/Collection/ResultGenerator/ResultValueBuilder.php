<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Registry\ValueLoaderRegistryInterface;
use Netgen\BlockManager\Collection\ResultValue;
use Netgen\BlockManager\Collection\ValueConverterInterface;
use RuntimeException;

class ResultValueBuilder implements ResultValueBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\ValueConverterInterface[]
     */
    protected $valueConverters = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     * @param \Netgen\BlockManager\Collection\ValueConverterInterface[] $valueConverters
     */
    public function __construct(
        ValueLoaderRegistryInterface $valueLoaderRegistry,
        array $valueConverters = array()
    ) {
        $this->valueLoaderRegistry = $valueLoaderRegistry;
        $this->valueConverters = $valueConverters;
    }

    /**
     * Builds the result value from provided object.
     *
     * @param mixed $object
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Collection\ResultValue
     */
    public function build($object)
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

            $resultValue = new ResultValue();

            $resultValue->id = $valueConverter->getId($object);
            $resultValue->type = $valueConverter->getValueType($object);
            $resultValue->name = $valueConverter->getName($object);
            $resultValue->isVisible = $valueConverter->getIsVisible($object);
            $resultValue->object = $object;

            return $resultValue;
        }

        throw new RuntimeException('No result value builder for object of type "' . get_class($object) . '".');
    }

    /**
     * Builds the result value from provided item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Collection\ResultValue
     */
    public function buildFromItem(Item $item)
    {
        $valueLoader = $this->valueLoaderRegistry
            ->getValueLoader($item->getValueType());

        $loadedValue = $valueLoader->load($item->getValueId());

        return $this->build($loadedValue);
    }
}
