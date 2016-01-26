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
     * Zone ID to which this block belongs.
     *
     * @var int|string
     */
    public $zoneId;

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
