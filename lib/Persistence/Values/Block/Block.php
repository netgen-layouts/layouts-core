<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Block extends Value
{
    use HydratorTrait;

    /**
     * Block ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * ID of the layout where block is located.
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
     * ID of the parent block or null if block has no parent.
     *
     * @var int|string|null
     */
    public $parentId;

    /**
     * Placeholder to which this block belongs in the parent block or null if block has no parent.
     *
     * @var string|null
     */
    public $placeholder;

    /**
     * Position of the block in the parent block or null if block has no parent.
     *
     * @var int|null
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
     * @var array[]
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
}
