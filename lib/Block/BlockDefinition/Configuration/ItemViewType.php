<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Utils\HydratorTrait;

final class ItemViewType
{
    use HydratorTrait;

    /**
     * Returns the item view type identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns the item view type name.
     */
    public private(set) string $name;
}
