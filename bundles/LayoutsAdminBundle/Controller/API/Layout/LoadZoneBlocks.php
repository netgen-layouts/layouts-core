<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\ArrayValue;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\NotFoundException;

final class LoadZoneBlocks extends AbstractController
{
    private LayoutService $layoutService;

    private BlockService $blockService;

    public function __construct(LayoutService $layoutService, BlockService $blockService)
    {
        $this->layoutService = $layoutService;
        $this->blockService = $blockService;
    }

    /**
     * Loads all zone blocks.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout does not exist in provided locale
     */
    public function __invoke(Zone $zone, string $locale): ArrayValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $layout = $zone->isPublished() ?
            $this->layoutService->loadLayout($zone->getLayoutId()) :
            $this->layoutService->loadLayoutDraft($zone->getLayoutId());

        if (!$layout->hasLocale($locale)) {
            throw new NotFoundException('layout', $layout->getId()->toString());
        }

        $blocks = [];
        foreach ($this->blockService->loadZoneBlocks($zone, [$locale]) as $block) {
            $blocks[] = new View($block);
        }

        return new ArrayValue($blocks);
    }
}
