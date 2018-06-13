<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Controller;

final class Edit extends Controller
{
    /**
     * Displays block edit interface.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block)
    {
        return $this->render(
            '@NetgenBlockManagerAdmin/app/block/edit.html.twig',
            [
                'block' => $block,
            ]
        );
    }
}
