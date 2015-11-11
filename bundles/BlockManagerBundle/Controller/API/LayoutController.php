<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
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
    public function getLayout(Layout $layout)
    {
        $layoutView = $this->buildViewObject($layout, array(), 'api');

        return $this->serializeObject($layoutView);
    }
}
