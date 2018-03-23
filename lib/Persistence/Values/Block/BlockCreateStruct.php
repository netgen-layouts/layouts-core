<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Value;

final class BlockCreateStruct extends Value
{
    /**
     * Status of the new block.
     *
     * @var int
     */
    public $status;

    /**
     * Position of the new block.
     *
     * @var int
     */
    public $position;

    /**
     * Identifier of the block definition of the new block.
     *
     * @var string
     */
    public $definitionIdentifier;

    /**
     * View type of the new block.
     *
     * @var string
     */
    public $viewType;

    /**
     * Item view type of the new block.
     *
     * @var string
     */
    public $itemViewType;

    /**
     * Human readable name of the new block.
     *
     * @var string
     */
    public $name;

    /**
     * Flag indicating if the block is always available.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * Flag indicating if the block is translatable.
     *
     * @var bool
     */
    public $isTranslatable;

    /**
     * The block parameters.
     *
     * @var array
     */
    public $parameters;

    /**
     * The block configuration.
     *
     * @var array
     */
    public $config;
}
