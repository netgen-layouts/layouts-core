<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LinkZone extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
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

        $sharedLayout = $this->layoutService->loadLayout($requestData->get('linked_layout_id'));

        $zoneIdentifier = $requestData->get('linked_zone_identifier');
        if (!$sharedLayout->hasZone($zoneIdentifier)) {
            throw new NotFoundException('zone', $zoneIdentifier);
        }

        $this->layoutService->linkZone($zone, $sharedLayout->getZone($zoneIdentifier));

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
