<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;

final class LoadLayoutBlocks extends Controller
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
     * Loads all layout blocks.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout does not exist in provided locale
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function __invoke(Layout $layout, $locale)
    {
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
