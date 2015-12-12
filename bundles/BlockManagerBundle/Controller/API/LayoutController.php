<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\View\ViewInterface;
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

        $normalizedLayout = $serializer->normalize(
            new SerializableValue(
                $layout,
                self::API_VERSION
            )
        );

        $layoutView = $this->buildViewObject(
            $layout,
            ViewInterface::CONTEXT_API,
            array('api_version' => self::API_VERSION)
        );

        $normalizedLayout['html'] = $this->renderViewObject($layoutView);

        $response = new JsonResponse();
        $response->setContent($serializer->encode($normalizedLayout, 'json'));

        return $response;
    }
}
