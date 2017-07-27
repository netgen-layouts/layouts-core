<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\ValueObject;

class LayoutCreateStruct extends ValueObject
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
     * Human readable description of the layout.
     *
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $status;

    /**
     * @var bool
     */
    public $shared;

    /**
     * @var bool
     */
    public $mainLocale;
}
