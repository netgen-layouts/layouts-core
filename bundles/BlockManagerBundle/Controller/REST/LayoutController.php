<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\REST;

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
        $layoutView = $this->buildViewObject($layout, array(), 'manager');

        return $this->serializeObject($layoutView);
    }
}
