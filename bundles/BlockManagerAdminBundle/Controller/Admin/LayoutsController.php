<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\HttpCache\ClientInterface;
use Netgen\BlockManager\Layout\Form\CopyType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LayoutsController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\HttpCache\ClientInterface
     */
    protected $httpCacheClient;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\HttpCache\ClientInterface $httpCacheClient
     */
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
            'NetgenBlockManagerAdminBundle:admin/layouts:index.html.twig',
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

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Clears the HTTP caches for provided blocks.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException if the list of block IDs in invalid
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clearBlocksCache(Layout $layout, Request $request)
    {
        if ($request->isMethod('POST')) {
            $blockIds = $request->request->get('blocks');
            if (!is_array($blockIds) || empty($blockIds)) {
                throw new BadStateException('blocks', 'List of block IDs needs to be a non-empty array.');
            }

            $this->httpCacheClient->invalidateBlocks($blockIds);

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        $cacheableBlocks = array_filter(
            $this->blockService->loadLayoutBlocks($layout),
            function (Block $block) {
                $blockConfig = $block->getConfig('http_cache');

                return $blockConfig->getParameter('use_http_cache')->getValue();
            }
        );

        return $this->render(
            'NetgenBlockManagerAdminBundle:admin/layouts/cache:blocks.html.twig',
            array(
                'layout' => $layout,
                'blocks' => array_values($cacheableBlocks),
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
