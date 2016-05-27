<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Loads a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function view(Layout $layout)
    {
        return new View($layout, Version::API_V1);
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
                $blocks[] = new View($block, Version::API_V1);
            }
        }

        return new ValueArray($blocks);
    }

    /**
     * Publishes a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function publish(Layout $layout)
    {
        $this->layoutService->publishLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
