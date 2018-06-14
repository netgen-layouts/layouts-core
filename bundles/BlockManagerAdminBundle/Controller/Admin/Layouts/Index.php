<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Layouts;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Symfony\Component\HttpFoundation\Response;

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
     */
    public function __invoke(): Response
    {
        return $this->render(
            '@NetgenBlockManagerAdmin/admin/layouts/index.html.twig',
            [
                'layouts' => $this->layoutService->loadLayouts(true),
            ]
        );
    }
}
