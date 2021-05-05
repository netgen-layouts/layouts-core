<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\Registry\BlockTypeRegistry;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\Block\BlockTypeException;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class Create extends AbstractController
{
    use ValidatorTrait;

    private BlockService $blockService;

    private CreateStructBuilder $createStructBuilder;

    private BlockTypeRegistry $blockTypeRegistry;

    public function __construct(
        BlockService $blockService,
        CreateStructBuilder $createStructBuilder,
        BlockTypeRegistry $blockTypeRegistry
    ) {
        $this->blockService = $blockService;
        $this->createStructBuilder = $createStructBuilder;
        $this->blockTypeRegistry = $blockTypeRegistry;
    }

    /**
     * Creates the block in specified block.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block type does not exist
     */
    public function __invoke(Block $block, Request $request): View
    {
        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        try {
            $blockType = $this->blockTypeRegistry->getBlockType($requestData->get('block_type'));
        } catch (BlockTypeException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        $this->denyAccessUnlessGranted(
            'nglayouts:block:add',
            [
                'block_definition' => $blockType->getDefinition(),
                'layout' => $block->getLayoutId()->toString(),
            ],
        );

        $blockCreateStruct = $this->createStructBuilder->buildCreateStruct($blockType);

        $createdBlock = $this->blockService->createBlock(
            $blockCreateStruct,
            $block,
            $requestData->get('parent_placeholder'),
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
            $data->get('parent_placeholder'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'parent_placeholder',
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
