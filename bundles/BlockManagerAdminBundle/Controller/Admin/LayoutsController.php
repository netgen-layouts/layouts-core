<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\HttpCache\ClientInterface;
use Netgen\BlockManager\Layout\Form\CopyType;
use Netgen\BlockManager\Transfer\Output\SerializerInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearBlocksCacheType;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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

    /**
     * @var \Netgen\BlockManager\Transfer\Output\SerializerInterface
     */
    private $transferSerializer;

    public function __construct(
        LayoutService $layoutService,
        BlockService $blockService,
        ClientInterface $httpCacheClient,
        SerializerInterface $transferSerializer
    ) {
        $this->layoutService = $layoutService;
        $this->blockService = $blockService;
        $this->httpCacheClient = $httpCacheClient;
        $this->transferSerializer = $transferSerializer;
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
            [
                'layouts' => $this->layoutService->loadLayouts(true),
            ]
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
            [
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'ngbm_admin_layouts_layout_copy',
                    [
                        'layoutId' => $layout->getId(),
                    ]
                ),
            ]
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
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Exports the provided list of layouts.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportLayouts(Request $request)
    {
        $layoutIds = array_unique($request->request->get('layout_ids'));

        $serializedLayouts = [];
        foreach ($layoutIds as $layoutId) {
            try {
                $layout = $this->layoutService->loadLayout($layoutId);
            } catch (NotFoundException $e) {
                continue;
            }

            $serializedLayouts[] = $this->transferSerializer->serializeLayout($layout);
        }

        $json = json_encode($serializedLayouts, JSON_PRETTY_PRINT);

        $response = new Response($json);

        $fileName = sprintf('layouts_export_%s.json', date('Y-m-d_H-i-s'));
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', $disposition);
        // X-Filename header is needed for AJAX file download support
        $response->headers->set('X-Filename', $fileName);

        return $response;
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
        $this->httpCacheClient->invalidateLayouts([$layout->getId()]);

        $cacheCleared = $this->httpCacheClient->commit();

        if ($cacheCleared) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->render(
            '@NetgenBlockManagerAdmin/admin/layouts/cache/layout.html.twig',
            [
                'error' => !$cacheCleared,
                'layout' => $layout,
            ],
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
                    if ($block->getDefinition()->hasPlugin(PagedCollectionsPlugin::class)) {
                        if ($block->getParameter('paged_collections:enabled')->getValue()) {
                            return true;
                        }
                    }

                    if (!$block->hasConfig('http_cache')) {
                        return false;
                    }

                    $blockConfig = $block->getConfig('http_cache');

                    return $blockConfig->getParameter('use_http_cache')->getValue();
                }
            )
        );

        $form = $this->createForm(
            ClearBlocksCacheType::class,
            null,
            [
                'blocks' => $cacheableBlocks,
                'action' => $this->generateUrl(
                    'ngbm_admin_layouts_cache_blocks',
                    [
                        'layoutId' => $layout->getId(),
                    ]
                ),
            ]
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
            [
                'error' => !$cacheCleared,
                'layout' => $layout,
                'blocks' => array_values($cacheableBlocks),
            ],
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
