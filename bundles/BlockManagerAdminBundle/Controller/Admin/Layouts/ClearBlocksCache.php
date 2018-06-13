<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Layouts;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\HttpCache\ClientInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearBlocksCacheType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ClearBlocksCache extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\BlockManager\HttpCache\ClientInterface
     */
    private $httpCacheClient;

    public function __construct(BlockService $blockService, ClientInterface $httpCacheClient)
    {
        $this->blockService = $blockService;
        $this->httpCacheClient = $httpCacheClient;
    }

    /**
     * Clears the HTTP caches for provided blocks.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Layout $layout, Request $request)
    {
        $cacheCleared = true;
        $cacheableBlocks = array_values(
            array_filter(
                $this->blockService->loadLayoutBlocks($layout),
                function (Block $block) {
                    if ($block->getDefinition()->hasPlugin(PagedCollectionsPlugin::class)) {
                        if ($block->getParameter('paged_collections:enabled')->getValue() === true) {
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
}
