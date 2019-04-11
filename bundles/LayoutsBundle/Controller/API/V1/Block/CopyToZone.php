<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CopyToZone extends AbstractController
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
     * Copies the block draft to specified zone.
     */
    public function __invoke(Block $block, Request $request): View
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:block:add',
            [
                'block_definition' => $block->getDefinition(),
                'layout' => $block->getLayoutId(),
            ]
        );

        $requestData = $request->attributes->get('data');

        $zone = $this->layoutService->loadZoneDraft(
            $requestData->get('layout_id'),
            $requestData->get('zone_identifier')
        );

        $copiedBlock = $this->blockService->copyBlockToZone(
            $block,
            $zone,
            $requestData->get('parent_position')
        );

        return new View($copiedBlock, Version::API_V1, Response::HTTP_CREATED);
    }
}
