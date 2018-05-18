<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\Value;

final class ItemCreateStruct extends Value implements ConfigAwareStruct
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

    /**
     * Type of the item. One of Item::TYPE_* constants.
     *
     * Required.
     *
     * @var int
     */
    public $type;
}
