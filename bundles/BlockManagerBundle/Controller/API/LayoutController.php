<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Values\Page\Layout;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $serializer = $this->get('serializer');

        $normalizedLayout = $this->normalizeValueObject($layout);

        $response = new JsonResponse();
        $response->setContent($serializer->encode($normalizedLayout, 'json'));

        return $response;
    }
}
