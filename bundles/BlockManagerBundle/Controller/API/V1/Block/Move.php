<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
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
     */
    public function __invoke(Block $block, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:block:reorder', ['layout' => $block->getLayoutId()]);

        $requestData = $request->attributes->get('data');

        $targetBlock = $this->blockService->loadBlockDraft(
            $requestData->get('parent_block_id')
        );

        $this->blockService->moveBlock(
            $block,
            $targetBlock,
            $requestData->get('parent_placeholder'),
            $requestData->get('parent_position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
