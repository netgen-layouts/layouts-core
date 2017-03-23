<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Persistence\Values\Value;

class Block extends Value
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
     * The depth of the block in the tree.
     *
     * @var int
     */
    public $depth;

    /**
     * Materialized path of the block.
     *
     * @var string
     */
    public $path;

    /**
     * ID of the parent block.
     *
     * @var int
     */
    public $parentId;

    /**
     * Placeholder to which this block belongs in the parent block.
     *
     * @var string
     */
    public $placeholder;

    /**
     * Position of the block in the parent block.
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
     * Block placeholder parameters.
     *
     * @var array
     */
    public $placeholderParameters;

    /**
     * Block parameters.
     *
     * @var array
     */
    public $parameters;

    /**
     * Block configuration.
     *
     * @var array
     */
    public $config;

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
     * Block status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
