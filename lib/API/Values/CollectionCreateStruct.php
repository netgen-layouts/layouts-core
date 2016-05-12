<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Value;

class CollectionCreateStruct extends Value
{
    /**
     * @var int
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $status = Collection::STATUS_DRAFT;
}
