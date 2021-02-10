<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

final class SlotCreateStruct
{
    /**
     * View type which will be used to render the item located at the new slot.
     */
    public ?string $viewType = null;
}
