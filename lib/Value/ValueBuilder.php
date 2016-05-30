<?php

namespace Netgen\BlockManager\Value;

use Netgen\BlockManager\Value\Registry\ValueLoaderRegistryInterface;
use RuntimeException;

class ValueBuilder implements ValueBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Value\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * @var \Netgen\BlockManager\Value\ValueConverterInterface[]
     */
    protected $valueConverters = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Value\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     * @param \Netgen\BlockManager\Value\ValueConverterInterface[] $valueConverters
     */
    public function __construct(
        ValueLoaderRegistryInterface $valueLoaderRegistry,
        array $valueConverters = array()
    ) {
        $this->valueLoaderRegistry = $valueLoaderRegistry;
        $this->valueConverters = $valueConverters;
    }

    /**
     * Builds the value from provided object.
     *
     * @param mixed $object
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Value\Value
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

            $value = new Value(
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
     * Builds the value from provided ID.
     *
     * @param int|string $valueId
     * @param string $valueType
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Value\Value
     */
    public function build($valueId, $valueType)
    {
        $valueLoader = $this->valueLoaderRegistry
            ->getValueLoader($valueType);

        $loadedValue = $valueLoader->load($valueId);

        return $this->buildFromObject($loadedValue);
    }
}
