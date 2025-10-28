<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\API\Values\Collection\Item;

interface VisibilityVoterInterface
{
    /**
     * Returned by the voter if the item is not visible.
     */
    final public const int NO = 0;

    /**
     * Returned by the voter if the item is visible.
     */
    final public const int YES = 1;

    /**
     * Returned by the voter if it cannot decide if the item is visible or not.
     */
    final public const int ABSTAIN = 2;

    /**
     * Returns if the item is visible. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     */
    public function vote(Item $item): int;
}
