<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

use Netgen\BlockManager\Persistence\Values\Value;

class Zone extends Value
{
    /**
     * Zone ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Layout ID to which this zone belongs.
     *
     * @var int|string
     */
    public $layoutId;

    /**
     * Zone identifier.
     *
     * @var string
     */
    public $identifier;
}
