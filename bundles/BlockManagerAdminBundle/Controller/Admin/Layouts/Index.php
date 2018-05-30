<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Layouts;

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
     * Displays the index page of layouts admin interface.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke()
    {
        return $this->render(
            '@NetgenBlockManagerAdmin/admin/layouts/index.html.twig',
            [
                'layouts' => $this->layoutService->loadLayouts(true),
            ]
        );
    }
}
