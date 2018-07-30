<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;

final class ItemCreateStruct implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;

    /**
     * The definition of the item which will be created.
     *
     * Required.
     *
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    public $definition;

    /**
     * The value stored within the item.
     *
     * Required.
     *
     * @var int|string
     */
    public $value;
}
