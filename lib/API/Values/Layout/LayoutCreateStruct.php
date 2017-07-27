<?php

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\ValueObject;

class LayoutCreateStruct extends ValueObject
{
    /**
     * Layout type from which the new layout will be created.
     *
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    public $layoutType;

    /**
     * Human readable name of the layout.
     *
     * @var string
     */
    public $name;

    /**
     * Description of the layout.
     *
     * @var string
     */
    public $description;

    /**
     * Specifies if this layout will be shared or not.
     *
     * @var bool
     */
    public $shared;

    /**
     * Specifies the main locale of the layout.
     *
     * @var string
     */
    public $mainLocale;
}
