<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Utils\HydratorTrait;

final class ItemUpdateStruct
{
    use HydratorTrait;

    /**
     * New item configuration.
     *
     * @var array|null
     */
    public $config;
}
