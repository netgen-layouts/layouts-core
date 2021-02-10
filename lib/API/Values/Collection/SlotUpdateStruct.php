<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

final class SlotUpdateStruct
{
    /**
     * View type which will be used to render the item located at the slot.
     *
     * Set to an empty string to remove the stored view type.
     */
    public ?string $viewType = null;
}
