<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\ValueObject;

class BlockUpdateStruct extends ValueObject
{
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
    public $config;
}
