<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\ValueObject;

class BlockUpdateStruct extends ValueObject
{
    /**
     * New view type of the block.
     *
     * @var string
     */
    public $viewType;

    /**
     * New item view type of the block.
     *
     * @var string
     */
    public $itemViewType;

    /**
     * New human readable name of the block.
     *
     * @var string
     */
    public $name;

    /**
     * Flag indicating if the block will be always available.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * Flag indicating if the block will be translatable.
     *
     * @var bool
     */
    public $isTranslatable;

    /**
     * New block configuration.
     *
     * @var array
     */
    public $config;
}
