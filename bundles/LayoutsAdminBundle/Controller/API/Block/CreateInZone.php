<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Block\Registry\BlockTypeRegistry;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\Block\BlockTypeException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Validator\ValidatorTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class CreateInZone extends AbstractController
{
    use ValidatorTrait;

    private BlockService $blockService;

    private LayoutService $layoutService;

    private CreateStructBuilder $createStructBuilder;

    private BlockTypeRegistry $blockTypeRegistry;

    public function __construct(
        BlockService $blockService,
        LayoutService $layoutService,
        CreateStructBuilder $createStructBuilder,
        BlockTypeRegistry $blockTypeRegistry
    ) {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
        $this->createStructBuilder = $createStructBuilder;
        $this->blockTypeRegistry = $blockTypeRegistry;
    }

    /**
     * Creates the block in specified zone.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block type does not exist
     */
    public function __invoke(Request $request): View
    {
        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        try {
            $blockType = $this->blockTypeRegistry->getBlockType($requestData->get('block_type'));
        } catch (BlockTypeException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString($requestData->get('layout_id')));

        $this->denyAccessUnlessGranted(
            'nglayouts:block:add',
            [
                'block_definition' => $blockType->getDefinition(),
                'layout' => $layout,
            ],
        );

        $zoneIdentifier = $requestData->get('zone_identifier');
        if (!$layout->hasZone($zoneIdentifier)) {
            throw new NotFoundException('zone', $zoneIdentifier);
        }

        $blockCreateStruct = $this->createStructBuilder->buildCreateStruct($blockType);

        $createdBlock = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $layout->getZone($zoneIdentifier),
            $requestData->get('parent_position'),
        );

        return new View($createdBlock, Response::HTTP_CREATED);
    }

    /**
     * Validates the provided parameter bag.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateRequestData(ParameterBag $data): void
    {
        $this->validate(
            $data->get('block_type'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'block_type',
        );

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
