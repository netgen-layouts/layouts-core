<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Move extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Moves the block draft to specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, Request $request)
    {
        $requestData = $request->attributes->get('data');

        $targetBlock = $this->blockService->loadBlockDraft(
            $requestData->get('block_id')
        );

        $this->blockService->moveBlock(
            $block,
            $targetBlock,
            $requestData->get('placeholder'),
            $requestData->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
