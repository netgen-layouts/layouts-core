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
     *
     * @var string|null
     */
    public $viewType;

    /**
     * New item configuration.
     *
     * @var array|null
     */
    public $config;
}
