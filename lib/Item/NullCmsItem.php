<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * NullCmsItem represents an item referencing a value from CMS which could not be
 * loaded (for example, if the value does not exist any more).
 */
final class NullCmsItem implements CmsItemInterface
{
    public function __construct(
        private string $valueType,
    ) {}

    public function getValue(): null
    {
        return null;
    }

    public function getRemoteId(): null
    {
        return null;
    }

    public function getValueType(): string
    {
        return $this->valueType;
    }

    public function getName(): string
    {
        return '(INVALID ITEM)';
    }

    public function isVisible(): true
    {
        return true;
    }

    public function getObject(): null
    {
        return null;
    }
}
