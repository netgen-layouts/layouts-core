<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\Layouts;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Index extends AbstractController
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
     * Displays the index page of layouts admin interface.
     */
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:ui:access');

        return $this->render(
            '@NetgenLayoutsAdmin/admin/layouts/index.html.twig',
            [
                'layouts' => $this->layoutService->loadLayouts(true),
            ]
        );
    }
}
