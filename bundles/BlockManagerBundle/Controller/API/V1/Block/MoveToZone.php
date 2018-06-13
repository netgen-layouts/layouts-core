<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveToZone extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(BlockService $blockService, LayoutService $layoutService)
    {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
    }

    /**
     * Moves the block draft to specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, Request $request)
    {
        $requestData = $request->attributes->get('data');

        $zone = $this->layoutService->loadZoneDraft(
            $requestData->get('layout_id'),
            $requestData->get('zone_identifier')
        );

        $this->blockService->moveBlockToZone(
            $block,
            $zone,
            $requestData->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
