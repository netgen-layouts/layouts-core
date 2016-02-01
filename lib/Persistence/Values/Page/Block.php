<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

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
     * Zone identifier to which this block belongs.
     *
     * @var string
     */
    public $zoneIdentifier;

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
