<?php

namespace Netgen\BlockManager\Core\Service\StructBuilder;

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
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct
     */
    public function newLayoutUpdateStruct()
    {
        return new LayoutUpdateStruct();
    }
}
