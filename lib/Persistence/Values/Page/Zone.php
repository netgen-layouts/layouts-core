<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

use Netgen\BlockManager\ValueObject;

class Zone extends ValueObject
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

    /**
     * ID of layout this zone is linked to.
     *
     * @var int
     */
    public $linkedLayoutId;

    /**
     * Zone identifier this zone is linked to.
     *
     * @var string
     */
    public $linkedZoneIdentifier;
}
