<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\Item\ValueException;

final class CmsItemBuilder implements CmsItemBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ValueConverterInterface[]
     */
    private $valueConverters;

    /**
     * @param \Netgen\BlockManager\Item\ValueConverterInterface[] $valueConverters
     */
    public function __construct(array $valueConverters)
    {
        $this->valueConverters = array_filter(
            $valueConverters,
            static function (ValueConverterInterface $valueConverter): bool {
                return true;
            }
        );
    }

    public function build($object): CmsItemInterface
    {
        foreach ($this->valueConverters as $valueConverter) {
            if (!$valueConverter->supports($object)) {
                continue;
            }

            return CmsItem::fromArray(
                [
                    'value' => $valueConverter->getId($object),
                    'remoteId' => $valueConverter->getRemoteId($object),
                    'valueType' => $valueConverter->getValueType($object),
                    'name' => $valueConverter->getName($object),
                    'isVisible' => $valueConverter->getIsVisible($object),
                    'object' => $valueConverter->getObject($object),
                ]
            );
        }

        throw ValueException::noValueConverter(get_class($object));
    }
}
