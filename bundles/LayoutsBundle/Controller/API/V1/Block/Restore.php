<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;

final class Restore extends AbstractController
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
     * Restores the block draft to the published state.
     */
    public function __invoke(Block $block): View
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:block:edit',
            [
                'block_definition' => $block->getDefinition(),
                'layout' => $block->getLayoutId(),
            ]
        );

        $restoredBlock = $this->blockService->restoreBlock($block);

        return new View($restoredBlock, Version::API_V1);
    }
}
