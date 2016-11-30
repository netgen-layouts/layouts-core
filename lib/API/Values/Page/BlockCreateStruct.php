<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\ParameterStruct;

class BlockCreateStruct extends ParameterStruct
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public $blockDefinition;

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
