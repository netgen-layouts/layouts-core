<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

final class ItemCreateStruct extends ValueObject
{
    /**
     * Position of the new item in the collection.
     *
     * @var int
     */
    public $position;

    /**
     * Value from CMS for the new item. This is usually the ID of the CMS entity.
     *
     * @var int|string
     */
    public $value;

    /**
     * Type of value from CMS for the new item.
     *
     * @var string
     */
    public $valueType;

    /**
     * Type of the item. One of Item::TYPE_* flags.
     *
     * @var int
     */
    public $type;

    /**
     * The item configuration.
     *
     * @var array
     */
    public $config;
}
