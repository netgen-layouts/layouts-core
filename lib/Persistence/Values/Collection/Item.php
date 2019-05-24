<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Item extends Value
{
    use HydratorTrait;

    /**
     * Item ID.
     *
     * @var int
     */
    public $id;

    /**
     * Item UUID.
     *
     * @var string
     */
    public $uuid;

    /**
     * ID of the collection to which this item belongs.
     *
     * @var int
     */
    public $collectionId;

    /**
     * UUID of the collection to which this item belongs.
     *
     * @var string
     */
    public $collectionUuid;

    /**
     * Position of item within the collection.
     *
     * @var int
     */
    public $position;

    /**
     * Value from CMS this item wraps. This is usually the ID of the CMS entity.
     *
     * @var int|string
     */
    public $value;

    /**
     * Type of value from CMS this item wraps.
     *
     * @var string
     */
    public $valueType;

    /**
     * View type which will be used to render this item.
     *
     * If null, the view type needs to be set from outside to render the item.
     *
     * @var string|null
     */
    public $viewType;

    /**
     * Item configuration.
     *
     * @var array
     */
    public $config;
}
