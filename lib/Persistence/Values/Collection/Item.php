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
     */
    public int $id;

    /**
     * Item UUID.
     */
    public string $uuid;

    /**
     * ID of the collection to which this item belongs.
     */
    public int $collectionId;

    /**
     * UUID of the collection to which this item belongs.
     */
    public string $collectionUuid;

    /**
     * Position of item within the collection.
     */
    public int $position;

    /**
     * Value from CMS this item wraps. This is usually the ID of the CMS entity.
     *
     * @var int|string|null
     */
    public $value;

    /**
     * Type of value from CMS this item wraps.
     */
    public string $valueType;

    /**
     * View type which will be used to render this item.
     *
     * If null, the view type needs to be set from outside to render the item.
     */
    public ?string $viewType;

    /**
     * Item configuration.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $config;
}
