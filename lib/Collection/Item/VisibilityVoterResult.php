<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

enum VisibilityVoterResult: int
{
    /**
     * Returned by the voter if the item is not visible.
     */
    case No = 0;

    /**
     * Returned by the voter if the item is visible.
     */
    case Yes = 1;

    /**
     * Returned by the voter if it cannot decide if the item is visible or not.
     */
    case Abstain = 2;
}
