<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Symfony\Component\HttpFoundation\Response;

final class Delete extends AbstractController
{
    public function __construct(
        private BlockService $blockService,
    ) {}

    /**
     * Deletes the block draft.
     */
    public function __invoke(Block $block): Response
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:block:delete',
            [
                'block_definition' => $block->getDefinition(),
                'layout' => $block->getLayoutId()->toString(),
            ],
        );

        $this->blockService->deleteBlock($block);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
