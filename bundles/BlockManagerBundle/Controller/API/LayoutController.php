<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutController extends Controller
{
    /**
     * Serializes the layout object.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function view(Layout $layout)
    {
        $data = $this->buildData($layout);

        return $this->buildResponse($data);
    }

    /**
     * Serializes the blocks from provided layout object.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function viewLayoutBlocks(Layout $layout)
    {
        $blocks = array();
        foreach ($layout->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                $blocks[] = $this->buildData($block);
            }
        }

        return $this->buildResponse($blocks);
    }
}
