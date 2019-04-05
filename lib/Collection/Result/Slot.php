<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\Item\CmsItemInterface;

/**
 * The slot represents a placeholder for a CMS item when executing
 * a context dependant query when there's no context.
 */
final class Slot implements CmsItemInterface
{
    public function getValue()
    {
        return 0;
    }

    public function getRemoteId()
    {
        return 0;
    }

    public function getValueType(): string
    {
        return 'slot';
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
