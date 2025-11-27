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
    public int $value {
        get => 0;
    }

    public int $remoteId {
        get => 0;
    }

    public string $valueType {
        get => 'unknown';
    }

    public string $name {
        get => '(UNKNOWN ITEM)';
    }

    public true $isVisible {
        get => true;
    }

    public null $object {
        get => null;
    }
}
