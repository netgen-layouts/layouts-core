<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveToZone extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(BlockService $blockService, LayoutService $layoutService)
    {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
    }

    /**
     * Moves the block draft to specified zone.
     */
    public function __invoke(Block $block, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:block:reorder', ['layout' => $block->getLayoutId()->toString()]);

        $requestData = $request->attributes->get('data');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString($requestData->get('layout_id')));

        $zoneIdentifier = $requestData->get('zone_identifier');
        if (!$layout->hasZone($zoneIdentifier)) {
            throw new NotFoundException('zone', $zoneIdentifier);
        }

        $this->blockService->moveBlockToZone(
            $block,
            $layout->getZone($zoneIdentifier),
            $requestData->get('parent_position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
