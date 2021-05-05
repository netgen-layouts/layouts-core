<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Validator\ValidatorTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class Copy extends AbstractController
{
    use ValidatorTrait;

    private BlockService $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Copies the block draft to specified block.
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

        $targetBlock = $this->blockService->loadBlockDraft(
            Uuid::fromString($requestData->get('parent_block_id')),
        );

        $copiedBlock = $this->blockService->copyBlock(
            $block,
            $targetBlock,
            $requestData->get('parent_placeholder'),
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
            $data->get('parent_block_id'),
            [
                new Constraints\NotBlank(),
                new Constraints\Uuid(),
            ],
            'parent_block_id',
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
