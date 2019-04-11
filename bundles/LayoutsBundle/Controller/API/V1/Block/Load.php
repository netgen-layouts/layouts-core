<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;

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
