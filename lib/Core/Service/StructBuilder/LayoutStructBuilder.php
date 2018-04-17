<?php

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Layout\Type\LayoutType;

final class LayoutStructBuilder
{
    /**
     * Creates a new layout create struct from the provided values.
     *
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     * @param string $name
     * @param string $mainLocale
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct
     */
    public function newLayoutCreateStruct(LayoutType $layoutType, $name, $mainLocale)
    {
        return new LayoutCreateStruct(
            [
                'layoutType' => $layoutType,
                'name' => $name,
                'mainLocale' => $mainLocale,
            ]
        );
    }

    /**
     * Creates a new layout update struct.
     *
     * If the layout is provided, initial data is copied from the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct
     */
    public function newLayoutUpdateStruct(Layout $layout = null)
    {
        $layoutUpdateStruct = new LayoutUpdateStruct();

        if (!$layout instanceof Layout) {
            return $layoutUpdateStruct;
        }

        $layoutUpdateStruct->name = $layout->getName();
        $layoutUpdateStruct->description = $layout->getDescription();

        return $layoutUpdateStruct;
    }

    /**
     * Creates a new layout copy struct.
     *
     * If the layout is provided, initial data is copied from the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct
     */
    public function newLayoutCopyStruct(Layout $layout = null)
    {
        $layoutCopyStruct = new LayoutCopyStruct();

        if (!$layout instanceof Layout) {
            return $layoutCopyStruct;
        }

        $layoutCopyStruct->name = $layout->getName() . ' (copy)';

        return $layoutCopyStruct;
    }
}
