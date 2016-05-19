<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

use Netgen\BlockManager\API\Values\AbstractValue;

class Zone extends AbstractValue
{
    /**
     * Zone identifier.
     *
     * @var string
     */
    public $identifier;

    /**
     * Layout ID to which this zone belongs.
     *
     * @var int|string
     */
    public $layoutId;

    /**
     * Zone status. One of Layout::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
