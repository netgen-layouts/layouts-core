<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\API\Values\Collection\Item;

interface VisibilityVoterInterface
{
    /**
     * Returns if the item is visible.
     */
    public function vote(Item $item): VisibilityVoterResult;
}
