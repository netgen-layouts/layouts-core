<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\StructBuilder;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;

final class LayoutStructBuilder
{
    /**
     * Creates a new layout create struct from the provided values.
     */
    public function newLayoutCreateStruct(LayoutTypeInterface $layoutType, string $name, string $mainLocale): LayoutCreateStruct
    {
        $struct = new LayoutCreateStruct();
        $struct->layoutType = $layoutType;
        $struct->name = $name;
        $struct->mainLocale = $mainLocale;

        return $struct;
    }

    /**
     * Creates a new layout update struct.
     *
     * If the layout is provided, initial data is copied from the layout.
     */
    public function newLayoutUpdateStruct(?Layout $layout = null): LayoutUpdateStruct
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
     */
    public function newLayoutCopyStruct(?Layout $layout = null): LayoutCopyStruct
    {
        $layoutCopyStruct = new LayoutCopyStruct();

        if (!$layout instanceof Layout) {
            $layoutCopyStruct->name = 'Layout (copy)';

            return $layoutCopyStruct;
        }

        $layoutCopyStruct->name = $layout->getName() . ' (copy)';

        return $layoutCopyStruct;
    }
}
