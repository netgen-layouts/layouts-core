<?php

namespace Netgen\BlockManager\Collection\Item\VisibilityResolver;

use Netgen\BlockManager\API\Values\Collection\Item;

interface VoterInterface
{
    /**
     * Returned by the voter if the item is visible.
     */
    const YES = true;

    /**
     * Returned by the voter if the item is not visible.
     */
    const NO = false;

    /**
     * Returned by the voter if it cannot decide if the item is visible or not.
     */
    const ABSTAIN = null;

    /**
     * Returns if the item should be visible. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return bool|null
     */
    public function vote(Item $item);
}
