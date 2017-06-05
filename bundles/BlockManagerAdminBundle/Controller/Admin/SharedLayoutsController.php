<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class SharedLayoutsController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Displays the index page of shared layouts admin interface.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render(
            'NetgenBlockManagerAdminBundle:admin/shared_layouts:index.html.twig',
            array(
                'shared_layouts' => $this->layoutService->loadSharedLayouts(true),
            )
        );
    }

    /**
     * Clears the HTTP caches for layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clearRelatedLayoutsCache(Layout $layout)
    {
        $relatedLayouts = $this->layoutService->loadRelatedLayouts($layout);

        return $this->render(
            'NetgenBlockManagerAdminBundle:admin/shared_layouts/cache:related_layouts.html.twig',
            array(
                'layout' => $layout,
                'related_layouts' => $relatedLayouts,
            )
        );
    }

    /**
     * Performs access checks on the controller.
     */
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_ADMIN');
    }
}
