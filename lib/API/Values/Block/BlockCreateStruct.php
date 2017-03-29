<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;

class BlockCreateStruct extends ParameterStruct implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public $definition;

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
}
