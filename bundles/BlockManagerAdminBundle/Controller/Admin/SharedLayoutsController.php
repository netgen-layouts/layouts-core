<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\HttpCache\ClientInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearLayoutsCacheType;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SharedLayoutsController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\HttpCache\ClientInterface
     */
    private $httpCacheClient;

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
            '@NetgenBlockManagerAdmin/admin/shared_layouts/index.html.twig',
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
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function clearRelatedLayoutsCache(Layout $layout, Request $request)
    {
        $cacheCleared = true;
        $relatedLayouts = $this->layoutService->loadRelatedLayouts($layout);

        $form = $this->createForm(
            ClearLayoutsCacheType::class,
            null,
            array(
                'layouts' => $relatedLayouts,
                'action' => $this->generateUrl(
                    'ngbm_admin_shared_layouts_cache_related_layouts',
                    array(
                        'layoutId' => $layout->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedLayouts = $form->get('layouts')->getData();

            $this->httpCacheClient->invalidateLayouts(
                array_map(
                    function (Layout $layout) {
                        return $layout->getId();
                    },
                    $selectedLayouts
                )
            );

            $cacheCleared = $this->httpCacheClient->commit();

            if ($cacheCleared) {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_ADMIN,
            array(
                'error' => !$cacheCleared,
                'layout' => $layout,
                'related_layouts' => array_values($relatedLayouts),
            ),
            new Response(
                null,
                $form->isSubmitted() || !$cacheCleared ?
                    Response::HTTP_UNPROCESSABLE_ENTITY :
                    Response::HTTP_OK
            )
        );
    }

    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_ADMIN');
    }
}
