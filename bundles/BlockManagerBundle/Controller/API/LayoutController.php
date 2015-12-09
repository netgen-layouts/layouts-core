<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\ViewInterface;

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
        $layoutView = $this->buildViewObject(
            $layout,
            ViewInterface::CONTEXT_API,
            array('api_version' => self::API_VERSION)
        );

        return $this->serializeObject($layoutView);
    }
}
