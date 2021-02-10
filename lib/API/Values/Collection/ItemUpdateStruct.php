<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\Config\ConfigAwareStruct;
use Netgen\Layouts\API\Values\Config\ConfigAwareStructTrait;

final class ItemUpdateStruct implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;

    /**
     * New view type for the item.
     *
     * Set to an empty string to remove the stored view type.
     */
    public ?string $viewType = null;
}
