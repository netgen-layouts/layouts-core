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
     * Block parameters. Keys are locales, values are parameters in the specific locale.
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
     * Returns if the block is translatable.
     *
     * @var bool
     */
    public $isTranslatable;

    /**
     * Returns the main locale of this block.
     *
     * @var string
     */
    public $mainLocale;

    /**
     * Returns the list of all locales available in this block.
     *
     * @var string[]
     */
    public $availableLocales;

    /**
     * Returns if main locale of this block will be always available.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * Block status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
