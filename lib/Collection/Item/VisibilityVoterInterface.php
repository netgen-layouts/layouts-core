<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;

interface VisibilityVoterInterface
{
    /**
     * Returned by the voter if the item is visible.
     */
    public const YES = true;

    /**
     * Returned by the voter if the item is not visible.
     */
    public const NO = false;

    /**
     * Returned by the voter if it cannot decide if the item is visible or not.
     */
    public const ABSTAIN = null;

    /**
     * Returns if the item is visible. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     */
    public function vote(Item $item): ?bool;
}
