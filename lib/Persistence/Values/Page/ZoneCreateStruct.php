<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

use Netgen\BlockManager\ValueObject;

class ZoneCreateStruct extends ValueObject
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var int|string
     */
    public $linkedLayoutId;

    /**
     * @var string
     */
    public $linkedZoneIdentifier;
}
