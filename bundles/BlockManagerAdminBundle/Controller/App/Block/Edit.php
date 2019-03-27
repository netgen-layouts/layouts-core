<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

final class Edit extends Controller
{
    /**
     * Displays block edit interface.
     */
    public function __invoke(Block $block): Response
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:block:edit',
            [
                'block_definition' => $block->getDefinition(),
                'layout' => $block->getLayoutId(),
            ]
        );

        return $this->render(
            '@NetgenBlockManagerAdmin/app/block/edit.html.twig',
            [
                'block' => $block,
            ]
        );
    }
}
