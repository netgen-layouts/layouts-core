<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Validator\ValidatorTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class CopyToZone extends AbstractController
{
    use ValidatorTrait;

    private BlockService $blockService;

    private LayoutService $layoutService;

    public function __construct(BlockService $blockService, LayoutService $layoutService)
    {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
    }

    /**
     * Copies the block draft to specified zone.
     */
    public function __invoke(Block $block, Request $request): View
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:block:add',
            [
                'block_definition' => $block->getDefinition(),
                'layout' => $block->getLayoutId()->toString(),
            ],
        );

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString($requestData->get('layout_id')));

        $zoneIdentifier = $requestData->get('zone_identifier');
        if (!$layout->hasZone($zoneIdentifier)) {
            throw new NotFoundException('zone', $zoneIdentifier);
        }

        $copiedBlock = $this->blockService->copyBlockToZone(
            $block,
            $layout->getZone($zoneIdentifier),
            $requestData->get('parent_position'),
        );

        return new View($copiedBlock, Response::HTTP_CREATED);
    }

    /**
     * Validates the provided parameter bag.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateRequestData(ParameterBag $data): void
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
                new Constraints\Type(['type' => 'string']),
            ],
            'zone_identifier',
        );

        $this->validate(
            $data->get('parent_position'),
            [
                new Constraints\Type(['type' => 'int']),
            ],
            'parent_position',
        );
    }
}
