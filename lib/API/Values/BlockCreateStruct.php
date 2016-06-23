<?php

namespace Netgen\BlockManager\API\Values;

class BlockCreateStruct extends ParameterStruct
{
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
}
