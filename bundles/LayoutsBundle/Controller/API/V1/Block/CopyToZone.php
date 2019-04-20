<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CopyToZone extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
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

        $layout = $this->layoutService->loadLayoutDraft($requestData->get('layout_id'));

        $copiedBlock = $this->blockService->copyBlockToZone(
            $block,
            $layout->getZone($requestData->get('zone_identifier')),
            $requestData->get('parent_position')
        );

        return new View($copiedBlock, Version::API_V1, Response::HTTP_CREATED);
    }
}
