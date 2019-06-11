<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Serializer\Values\View;

final class Load extends AbstractController
{
    /**
     * Loads a block.
     */
    public function __invoke(Block $block): View
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        return new View($block);
    }
}
