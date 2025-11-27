<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Item\CmsItemInterface;

final class ManualItem implements CmsItemInterface
{
    public function __construct(
        private Item $collectionItem,
    ) {}

    /**
     * Returns the collection item that was used to generate this manual item.
     */
    public function getCollectionItem(): Item
    {
        return $this->collectionItem;
    }

    public function getValue(): int|string|null
    {
        return $this->collectionItem->cmsItem->getValue();
    }

    public function getRemoteId(): int|string|null
    {
        return $this->collectionItem->cmsItem->getRemoteId();
    }

    public function getValueType(): string
    {
        return $this->collectionItem->cmsItem->getValueType();
    }

    public function getName(): string
    {
        return $this->collectionItem->cmsItem->getName();
    }

    public function isVisible(): bool
    {
        return $this->collectionItem->cmsItem->isVisible();
    }

    public function getObject(): ?object
    {
        return $this->collectionItem->cmsItem->getObject();
    }
}
