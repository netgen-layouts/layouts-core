<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigUpdateAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigUpdateAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;

class BlockUpdateStruct extends ParameterStruct implements ConfigUpdateAwareStruct
{
    use ConfigUpdateAwareStructTrait;

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
