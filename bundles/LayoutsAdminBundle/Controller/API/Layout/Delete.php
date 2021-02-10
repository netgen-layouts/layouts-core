<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Symfony\Component\HttpFoundation\Response;

final class Delete extends AbstractController
{
    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Deletes a layout.
     */
    public function __invoke(Layout $layout): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:delete');

        $this->layoutService->deleteLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
