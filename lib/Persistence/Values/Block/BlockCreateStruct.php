<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\ValueObject;

class BlockCreateStruct extends ValueObject
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var int
     */
    public $status;

    /**
     * @var int
     */
    public $position;

    /**
     * @var string
     */
    public $definitionIdentifier;

    /**
     * @var string
     */
    public $viewType;

    /**
     * @var string
     */
    public $itemViewType;

    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $placeholderParameters;

    /**
     * @var array
     */
    public $parameters;

    /**
     * @var array
     */
    public $config;
}
