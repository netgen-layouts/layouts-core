<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Netgen\Layouts\Item\CmsItemInterface;

/**
 * This class represents a placeholder for a CMS item when executing
 * a context dependant query when there's no context.
 */
final class UnknownItem implements CmsItemInterface
{
    public function getValue(): int
    {
        return 0;
    }

    public function getRemoteId(): int
    {
        return 0;
    }

    public function getValueType(): string
    {
        return 'unknown';
    }

    public function getName(): string
    {
        return '(UNKNOWN ITEM)';
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
