<?php

namespace Netgen\BlockManager\API\Values;

class LayoutCreateStruct extends Value
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var @string
     */
    public $name;

    /**
     * @var string[]
     */
    public $zoneIdentifiers = array();
}
