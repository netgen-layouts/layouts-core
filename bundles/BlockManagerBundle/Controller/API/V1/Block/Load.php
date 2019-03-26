<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

final class Load extends Controller
{
    /**
     * Loads a block.
     */
    public function __invoke(Block $block): View
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');

        return new View($block, Version::API_V1);
    }
}
