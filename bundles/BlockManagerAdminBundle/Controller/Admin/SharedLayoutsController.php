<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\HttpCache\ClientInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SharedLayoutsController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\HttpCache\ClientInterface
     */
    protected $httpCacheClient;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\HttpCache\ClientInterface $httpCacheClient
     */
    public function __construct(LayoutService $layoutService, ClientInterface $httpCacheClient)
    {
        $this->layoutService = $layoutService;
        $this->httpCacheClient = $httpCacheClient;
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException if the list of layout IDs in invalid
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clearRelatedLayoutsCache(Layout $layout, Request $request)
    {
        if ($request->isMethod('POST')) {
            $layoutIds = $request->request->get('layouts');
            if (!is_array($layoutIds) || empty($layoutIds)) {
                throw new BadStateException('layouts', 'List of layout IDs needs to be a non-empty array.');
            }

            $this->httpCacheClient->invalidateLayouts($layoutIds);

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

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
