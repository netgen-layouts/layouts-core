<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

final class LayoutCreateStruct
{
    /**
     * Layout type from which the new layout will be created.
     *
     * Required.
     *
     * @var \Netgen\Layouts\Layout\Type\LayoutTypeInterface
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
     * @var string|null
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
