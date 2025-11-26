<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Symfony\Component\HttpFoundation\Response;

final class UnlinkZone extends AbstractController
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    /**
     * Removes the zone link, if any exists.
     */
    public function __invoke(Zone $zone): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:edit', ['layout' => $zone->layoutId->toString()]);

        $this->layoutService->unlinkZone($zone);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
