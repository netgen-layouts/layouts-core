<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;

final class LoadZoneBlocks extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(LayoutService $layoutService, BlockService $blockService)
    {
        $this->layoutService = $layoutService;
        $this->blockService = $blockService;
    }

    /**
     * Loads all zone blocks.
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout does not exist in provided locale
     */
    public function __invoke(Zone $zone, string $locale): Value
    {
        $layout = $zone->isPublished() ?
            $this->layoutService->loadLayout($zone->getLayoutId()) :
            $this->layoutService->loadLayoutDraft($zone->getLayoutId());

        if (!$layout->hasLocale($locale)) {
            throw new NotFoundException('layout', $layout->getId());
        }

        $blocks = [];
        foreach ($this->blockService->loadZoneBlocks($zone, [$locale]) as $block) {
            $blocks[] = new View($block, Version::API_V1);
        }

        return new Value($blocks);
    }
}
