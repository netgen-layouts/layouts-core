<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\Config\ConfigAwareValue;
use Netgen\Layouts\API\Values\Config\ConfigAwareValueTrait;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Collection\Item\ItemDefinitionInterface;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Item implements Value, ConfigAwareValue
{
    use ConfigAwareValueTrait;
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) UuidInterface $id;

    /**
     * Returns the UUID of the collection to which the item belongs.
     */
    public private(set) UuidInterface $collectionId;

    /**
     * Returns the item definition.
     */
    public private(set) ItemDefinitionInterface $definition;

    /**
     * Returns the item position within the collection.
     */
    public private(set) int $position;

    /**
     * Returns the value stored inside the collection item.
     */
    public private(set) int|string|null $value;

    /**
     * View type which will be used to render this item.
     *
     * If null, the view type needs to be set from outside to render the item.
     */
    public private(set) ?string $viewType;

    /**
     * Returns the CMS item loaded from value and value type stored in this collection item.
     */
    public private(set) CmsItemInterface $cmsItem;

    /**
     * Returns if the item is valid. An item is valid if the CMS item it wraps is visible and
     * if it actually exists in the CMS.
     */
    public bool $isValid {
        get {
            if ($this->cmsItem instanceof NullCmsItem) {
                return false;
            }

            return $this->cmsItem->isVisible();
        }
    }
}
