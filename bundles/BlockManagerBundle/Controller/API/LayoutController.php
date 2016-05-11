<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Values\ValueArray;

class LayoutController extends Controller
{
    /**
     * Loads a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function view(Layout $layout)
    {
        return new View($layout, self::API_VERSION);
    }

    /**
     * Loads all layout blocks.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function viewLayoutBlocks(Layout $layout)
    {
        $blocks = array();
        foreach ($layout->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                $blocks[] = new View($block, self::API_VERSION);
            }
        }

        return new ValueArray($blocks);
    }
}
