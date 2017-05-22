<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;

class BlockUpdateStruct extends ParameterStruct implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;

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
}
