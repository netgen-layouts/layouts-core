<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Value;

class LayoutCreateStruct extends Value
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

    /**
     * @var string[]
     */
    public $zoneIdentifiers = array();
}
