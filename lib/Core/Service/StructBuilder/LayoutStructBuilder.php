<?php

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Layout\Type\LayoutType;

class LayoutStructBuilder
{
    /**
     * Creates a new layout create struct.
     *
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct
     */
    public function newLayoutCreateStruct(LayoutType $layoutType, $name)
    {
        return new LayoutCreateStruct(
            array(
                'layoutType' => $layoutType,
                'name' => $name,
            )
        );
    }

    /**
     * Creates a new layout update struct.
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
