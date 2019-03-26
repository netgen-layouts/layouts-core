<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

final class LoadLayoutBlocks extends Controller
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
     * Loads all layout blocks.
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout does not exist in provided locale
     */
    public function __invoke(Layout $layout, string $locale): Value
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');

        if (!$layout->hasLocale($locale)) {
            throw new NotFoundException('layout', $layout->getId());
        }

        $blocks = [];
        foreach ($layout as $zone) {
            foreach ($this->blockService->loadZoneBlocks($zone, [$locale]) as $block) {
                $blocks[] = new View($block, Version::API_V1);
            }
        }

        return new Value($blocks);
    }
}
