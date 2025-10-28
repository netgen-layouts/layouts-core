<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\SharedLayouts;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Symfony\Component\HttpFoundation\Response;

final class Index extends AbstractController
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    /**
     * Displays the index page of shared layouts admin interface.
     */
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:ui:access');

        return $this->render(
            '@NetgenLayoutsAdmin/admin/shared_layouts/index.html.twig',
            [
                'shared_layouts' => $this->layoutService->loadSharedLayouts(true),
            ],
        );
    }
}
