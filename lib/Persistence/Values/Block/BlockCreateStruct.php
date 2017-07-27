<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\ValueObject;

class BlockCreateStruct extends ValueObject
{
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
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * @var bool
     */
    public $isTranslatable;

    /**
     * @var array
     */
    public $parameters;

    /**
     * @var array
     */
    public $config;
}
