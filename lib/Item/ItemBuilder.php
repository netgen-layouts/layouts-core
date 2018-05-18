<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\Item\ValueException;

final class ItemBuilder implements ItemBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ValueConverterInterface[]
     */
    private $valueConverters = [];

    /**
     * @param \Netgen\BlockManager\Item\ValueConverterInterface[] $valueConverters
     */
    public function __construct(array $valueConverters = [])
    {
        $this->valueConverters = array_filter(
            $valueConverters,
            function (ValueConverterInterface $valueConverter) {
                return $valueConverter;
            }
        );
    }

    public function build($object)
    {
        foreach ($this->valueConverters as $valueConverter) {
            if (!$valueConverter->supports($object)) {
                continue;
            }

            $value = new Item(
                [
                    'value' => $valueConverter->getId($object),
                    'remoteId' => $valueConverter->getRemoteId($object),
                    'valueType' => $valueConverter->getValueType($object),
                    'name' => $valueConverter->getName($object),
                    'isVisible' => $valueConverter->getIsVisible($object),
                    'object' => $valueConverter->getObject($object),
                ]
            );

            return $value;
        }

        throw ValueException::noValueConverter(
            is_object($object) ? get_class($object) : gettype($object)
        );
    }
}
