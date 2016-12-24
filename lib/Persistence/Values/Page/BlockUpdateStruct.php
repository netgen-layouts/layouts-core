<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

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
     * @var array
     */
    public $placeholderParameters;

    /**
     * @var array
     */
    public $parameters;
}
