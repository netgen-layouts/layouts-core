<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;

final class Load extends AbstractController
{
    /**
     * Loads a block.
     */
    public function __invoke(Block $block): View
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        return new View($block, Version::API_V1);
    }
}
