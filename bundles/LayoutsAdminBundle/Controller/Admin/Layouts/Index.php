<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\Layouts;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Symfony\Component\HttpFoundation\Response;

final class Index extends AbstractController
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    /**
     * Displays the index page of layouts admin interface.
     */
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:ui:access');

        return $this->render(
            '@NetgenLayoutsAdmin/admin/layouts/index.html.twig',
            [
                'layouts' => $this->layoutService->loadLayouts(true),
            ],
        );
    }
}
