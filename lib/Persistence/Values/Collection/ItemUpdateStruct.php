<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

final class ItemUpdateStruct extends ValueObject
{
    /**
     * New item configuration.
     *
     * @var array
     */
    public $config;
}
