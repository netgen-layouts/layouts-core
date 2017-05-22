<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;

class BlockCreateStruct extends ParameterStruct implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;

    /**
     * Block definition to create the new block from.
     *
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public $definition;

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
     * Human readable name of the block.
     *
     * @var string
     */
    public $name;
}
