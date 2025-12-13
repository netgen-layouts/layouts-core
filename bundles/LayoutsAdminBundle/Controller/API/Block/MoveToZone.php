<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints;

final class MoveToZone extends AbstractController
{
    use ValidatorTrait;

    public function __construct(
        private BlockService $blockService,
        private LayoutService $layoutService,
    ) {}

    /**
     * Moves the block draft to specified zone.
     */
    public function __invoke(Block $block, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:block:reorder', ['layout' => $block->layoutId->toString()]);

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString($requestData->getString('layout_id')));

        $zoneIdentifier = $requestData->getString('zone_identifier');
        if (!$layout->hasZone($zoneIdentifier)) {
            throw new NotFoundException('zone', $zoneIdentifier);
        }

        $this->blockService->moveBlockToZone(
            $block,
            $layout->getZone($zoneIdentifier),
            $requestData->getInt('parent_position'),
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validates the provided input bag.
     *
     * @param \Symfony\Component\HttpFoundation\InputBag<int|string> $data
     */
    private function validateRequestData(InputBag $data): void
    {
        $this->validate(
            $data->get('layout_id'),
            [
                new Constraints\NotBlank(),
                new Constraints\Uuid(),
            ],
            'layout_id',
        );

        $this->validate(
            $data->get('zone_identifier'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(type: 'string'),
            ],
            'zone_identifier',
        );

        $this->validate(
            $data->get('parent_position'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(type: 'int'),
            ],
            'parent_position',
        );
    }
}
