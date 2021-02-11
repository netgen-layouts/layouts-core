<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * NullCmsItem represents an item referencing a value from CMS which could not be
 * loaded (for example, if the value does not exist any more).
 */
final class NullCmsItem implements CmsItemInterface
{
    private string $valueType;

    public function __construct(string $valueType)
    {
        $this->valueType = $valueType;
    }

    public function getValue(): ?int
    {
        return null;
    }

    public function getRemoteId(): ?int
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

    public function isVisible(): bool
    {
        return true;
    }

    public function getObject(): ?object
    {
        return null;
    }
}
