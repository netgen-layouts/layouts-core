<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Serializer\Values\View;

final class Restore extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
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
                'layout' => $block->getLayoutId()->toString(),
            ]
        );

        $restoredBlock = $this->blockService->restoreBlock($block);

        return new View($restoredBlock);
    }
}
