<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\HttpCache\ClientInterface;
use Netgen\BlockManager\Layout\Form\CopyType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearBlocksCacheType;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LayoutsController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\BlockManager\HttpCache\ClientInterface
     */
    private $httpCacheClient;

    public function __construct(
        LayoutService $layoutService,
        BlockService $blockService,
        ClientInterface $httpCacheClient
    ) {
        $this->layoutService = $layoutService;
        $this->blockService = $blockService;
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
            '@NetgenBlockManagerAdmin/admin/layouts/index.html.twig',
            array(
                'layouts' => $this->layoutService->loadLayouts(true),
            )
        );
    }

    /**
     * Copies a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function copyLayout(Layout $layout, Request $request)
    {
        $copyStruct = $this->layoutService->newLayoutCopyStruct($layout);

        $form = $this->createForm(
            CopyType::class,
            $copyStruct,
            array(
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'ngbm_admin_layouts_layout_copy',
                    array(
                        'layoutId' => $layout->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

            return $this->buildView($copiedLayout, ViewInterface::CONTEXT_ADMIN);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_ADMIN,
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Deletes a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLayout(Layout $layout)
    {
        $this->layoutService->deleteLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Clears the HTTP cache for provided layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clearLayoutCache(Layout $layout)
    {
        $this->httpCacheClient->invalidateLayouts(array($layout->getId()));

        $cacheCleared = $this->httpCacheClient->commit();

        if ($cacheCleared) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->render(
            '@NetgenBlockManagerAdmin/admin/layouts/cache/layout.html.twig',
            array(
                'error' => !$cacheCleared,
                'layout' => $layout,
            ),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Clears the HTTP caches for provided blocks.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function clearBlocksCache(Layout $layout, Request $request)
    {
        $cacheCleared = true;
        $cacheableBlocks = array_values(
            array_filter(
                $this->blockService->loadLayoutBlocks($layout),
                function (Block $block) {
                    $blockConfig = $block->getConfig('http_cache');

                    return $blockConfig->getParameter('use_http_cache')->getValue();
                }
            )
        );

        $form = $this->createForm(
            ClearBlocksCacheType::class,
            null,
            array(
                'blocks' => $cacheableBlocks,
                'action' => $this->generateUrl(
                    'ngbm_admin_layouts_cache_blocks',
                    array(
                        'layoutId' => $layout->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedBlocks = $form->get('blocks')->getData();

            $this->httpCacheClient->invalidateBlocks(
                array_map(
                    function (Block $block) {
                        return $block->getId();
                    },
                    $selectedBlocks
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
                'blocks' => array_values($cacheableBlocks),
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
