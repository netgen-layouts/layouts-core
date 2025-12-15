<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

enum VisibilityVoterResult
{
    /**
     * Returned by the voter if the item is not visible.
     */
    case No;

    /**
     * Returned by the voter if the item is visible.
     */
    case Yes;

    /**
     * Returned by the voter if it cannot decide if the item is visible or not.
     */
    case Abstain;
}
