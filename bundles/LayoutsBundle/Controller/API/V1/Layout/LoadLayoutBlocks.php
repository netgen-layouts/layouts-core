<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Serializer\Values\Value;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;

final class LoadLayoutBlocks extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Loads all layout blocks.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout does not exist in provided locale
     */
    public function __invoke(Layout $layout, string $locale): Value
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        if (!$layout->hasLocale($locale)) {
            throw new NotFoundException('layout', $layout->getId()->toString());
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
