<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\Item\ItemInterface;

/**
 * The slot represents a placeholder for an item when executing
 * a context dependant query when there's no context.
 */
final class Slot implements ItemInterface
{
    public function getValue()
    {
        return 0;
    }

    public function getRemoteId()
    {
        return 0;
    }

    public function getValueType()
    {
        return 'slot';
    }

    public function getName()
    {
        return '(UNKNOWN ITEM)';
    }

    public function isVisible()
    {
        return true;
    }

    public function getObject()
    {
    }
}
