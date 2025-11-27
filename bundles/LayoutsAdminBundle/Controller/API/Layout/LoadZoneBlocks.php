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
    public function __construct(
        private LayoutService $layoutService,
        private BlockService $blockService,
    ) {}

    /**
     * Loads all zone blocks.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout does not exist in provided locale
     */
    public function __invoke(Zone $zone, string $locale): ArrayValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $layout = $zone->isPublished ?
            $this->layoutService->loadLayout($zone->layoutId) :
            $this->layoutService->loadLayoutDraft($zone->layoutId);

        if (!$layout->hasLocale($locale)) {
            throw new NotFoundException('layout', $layout->id->toString());
        }

        $blocks = [];
        foreach ($this->blockService->loadZoneBlocks($zone, [$locale]) as $block) {
            $blocks[] = new View($block);
        }

        return new ArrayValue($blocks);
    }
}
