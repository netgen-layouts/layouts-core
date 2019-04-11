<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LinkZone extends AbstractController
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Links the provided zone to zone from shared layout.
     */
    public function __invoke(Zone $zone, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:edit', ['layout' => $zone->getLayoutId()]);

        $requestData = $request->attributes->get('data');

        $linkedZone = $this->layoutService->loadZone(
            $requestData->get('linked_layout_id'),
            $requestData->get('linked_zone_identifier')
        );

        $this->layoutService->linkZone($zone, $linkedZone);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
