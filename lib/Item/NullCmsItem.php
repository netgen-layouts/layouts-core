<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * NullCmsItem represents an item referencing a value from CMS which could not be
 * loaded (for example, if the value does not exist any more).
 */
final class NullCmsItem implements CmsItemInterface
{
    public null $value {
        get => null;
    }

    public null $remoteId {
        get => null;
    }

    public string $name {
        get => '(INVALID ITEM)';
    }

    public true $isVisible {
        get => true;
    }

    public null $object {
        get => null;
    }

    public function __construct(
        public private(set) string $valueType,
    ) {}
}
