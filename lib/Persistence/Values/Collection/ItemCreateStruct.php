<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Utils\HydratorTrait;

final class ItemCreateStruct
{
    use HydratorTrait;

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
     * The item configuration.
     *
     * @var array
     */
    public $config;
}
