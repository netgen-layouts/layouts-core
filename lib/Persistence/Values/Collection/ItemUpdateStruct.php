<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class ItemUpdateStruct
{
    use HydratorTrait;

    /**
     * New view type for the item.
     *
     * Set to an empty string to remove the stored view type.
     */
    public ?string $viewType = null;

    /**
     * New item configuration.
     *
     * @var array<string, array<string, mixed>>|null
     */
    public ?array $config = null;
}
