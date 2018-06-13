<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\SharedLayouts;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;

final class Index extends Controller
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
     * Displays the index page of shared layouts admin interface.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke()
    {
        return $this->render(
            '@NetgenBlockManagerAdmin/admin/shared_layouts/index.html.twig',
            [
                'shared_layouts' => $this->layoutService->loadSharedLayouts(true),
            ]
        );
    }
}
