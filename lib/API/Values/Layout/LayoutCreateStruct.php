<?php

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\Value;

final class LayoutCreateStruct extends Value
{
    /**
     * Layout type from which the new layout will be created.
     *
     * Required.
     *
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    public $layoutType;

    /**
     * Human readable name of the layout.
     *
     * Required.
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
    public $shared = false;

    /**
     * Specifies the main locale of the layout.
     *
     * Required.
     *
     * @var string
     */
    public $mainLocale;
}
