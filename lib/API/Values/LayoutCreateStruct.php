<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutCreateStruct extends AbstractValue
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $status = Layout::STATUS_DRAFT;
}
