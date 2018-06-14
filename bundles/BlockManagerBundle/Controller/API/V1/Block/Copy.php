<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Copy extends Controller
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
     * Copies the block draft to specified block.
     */
    public function __invoke(Block $block, Request $request): View
    {
        $requestData = $request->attributes->get('data');

        $targetBlock = $this->blockService->loadBlockDraft(
            $requestData->get('block_id')
        );

        $copiedBlock = $this->blockService->copyBlock(
            $block,
            $targetBlock,
            $requestData->get('placeholder'),
            $requestData->get('position')
        );

        return new View($copiedBlock, Version::API_V1, Response::HTTP_CREATED);
    }
}
