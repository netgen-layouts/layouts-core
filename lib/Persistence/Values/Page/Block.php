<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

use Netgen\BlockManager\ValueObject;

class Block extends ValueObject
{
    /**
     * Block ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Layout ID.
     *
     * @var int|string
     */
    public $layoutId;

    /**
     * Zone identifier to which this block belongs.
     *
     * @var string
     */
    public $zoneIdentifier;

    /**
     * Position of the block in the zone.
     *
     * @var int
     */
    public $position;

    /**
     * Block definition identifier.
     *
     * @var string
     */
    public $definitionIdentifier;

    /**
     * Block parameters.
     *
     * @var array
     */
    public $parameters;

    /**
     * View type which will be used to render this block.
     *
     * @var string
     */
    public $viewType;

    /**
     * Item view type which will be used to render block items.
     *
     * @var string
     */
    public $itemViewType;

    /**
     * Human readable name of this block.
     *
     * @var string
     */
    public $name;

    /**
     * Block status. One of Layout::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
