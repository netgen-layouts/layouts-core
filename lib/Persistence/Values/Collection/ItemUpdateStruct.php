<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Value;

final class ItemUpdateStruct extends Value
{
    /**
     * New item configuration.
     *
     * @var array|null
     */
    public $config;
}
