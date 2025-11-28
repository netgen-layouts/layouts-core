<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Item\CmsItemInterface;

final class ManualItem implements CmsItemInterface
{
    public int|string|null $value {
        get => $this->collectionItem->cmsItem->value;
    }

    public int|string|null $remoteId {
        get => $this->collectionItem->cmsItem->remoteId;
    }

    public string $valueType {
        get => $this->collectionItem->cmsItem->valueType;
    }

    public string $name {
        get => $this->collectionItem->cmsItem->name;
    }

    public bool $isVisible {
        get => $this->collectionItem->cmsItem->isVisible;
    }

    public ?object $object {
        get => $this->collectionItem->cmsItem->object;
    }

    public function __construct(
        /**
         * Returns the collection item that was used to generate this manual item.
         */
        public private(set) Item $collectionItem,
    ) {}
}
