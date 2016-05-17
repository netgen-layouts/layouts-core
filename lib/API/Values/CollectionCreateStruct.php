<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\API\Values\Collection\Collection;

class CollectionCreateStruct extends Value
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $type = Collection::TYPE_MANUAL;

    /**
     * @var int
     */
    public $status = Collection::STATUS_DRAFT;
}
