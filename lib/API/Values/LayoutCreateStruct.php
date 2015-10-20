<?php

namespace Netgen\BlockManager\API\Values;

class LayoutCreateStruct extends Value
{
    /**
     * @var int|string
     */
    public $parentId;

    /**
     * @var string
     */
    public $layoutIdentifier;

    /**
     * @var string[]
     */
    public $zoneIdentifiers;
}
