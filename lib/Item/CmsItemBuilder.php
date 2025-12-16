<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

use Netgen\Layouts\Exception\Item\ValueException;

final class CmsItemBuilder implements CmsItemBuilderInterface
{
    /**
     * @param iterable<\Netgen\Layouts\Item\ValueConverterInterface<object>> $valueConverters
     */
    public function __construct(
        private iterable $valueConverters,
    ) {}

    public function build(object $object): CmsItemInterface
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
                ],
            );
        }

        throw ValueException::noValueConverter($object::class);
    }
}
