<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Collection\Registry\ItemDefinitionRegistry;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

use function sprintf;

final class AddItems extends AbstractController
{
    use ValidatorTrait;

    private CollectionService $collectionService;

    private ItemDefinitionRegistry $itemDefinitionRegistry;

    public function __construct(
        CollectionService $collectionService,
        ItemDefinitionRegistry $itemDefinitionRegistry
    ) {
        $this->collectionService = $collectionService;
        $this->itemDefinitionRegistry = $itemDefinitionRegistry;
    }

    /**
     * Adds an item inside the collection.
     */
    public function __invoke(Block $block, string $collectionIdentifier, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($block, $collectionIdentifier, $requestData);

        $this->collectionService->transaction(
            function () use ($block, $collectionIdentifier, $requestData): void {
                foreach ($requestData->get('items') as $item) {
                    $itemCreateStruct = $this->collectionService->newItemCreateStruct(
                        $this->itemDefinitionRegistry->getItemDefinition($item['value_type']),
                        $item['value'],
                    );

                    $this->collectionService->addItem(
                        $block->getCollection($collectionIdentifier),
                        $itemCreateStruct,
                        $item['position'] ?? null,
                    );
                }
            },
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validates the provided parameter bag.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateRequestData(Block $block, string $collectionIdentifier, ParameterBag $data): void
    {
        $items = $data->get('items');

        $this->validate(
            $items,
            [
                new Constraints\Type(['type' => 'array']),
                new Constraints\NotBlank(),
                new Constraints\All(
                    [
                        'constraints' => new Constraints\Collection(
                            [
                                'fields' => [
                                    'value' => [
                                        new Constraints\NotNull(),
                                        new Constraints\Type(['type' => 'scalar']),
                                    ],
                                    'value_type' => [
                                        new Constraints\NotBlank(),
                                        new Constraints\Type(['type' => 'string']),
                                    ],
                                    'position' => new Constraints\Optional(
                                        [
                                            new Constraints\NotNull(),
                                            new Constraints\Type(['type' => 'int']),
                                        ],
                                    ),
                                ],
                            ],
                        ),
                    ],
                ),
            ],
            'items',
        );

        $blockDefinition = $block->getDefinition();
        if (!$blockDefinition->hasCollection($collectionIdentifier)) {
            return;
        }

        $collectionConfig = $blockDefinition->getCollection($collectionIdentifier);

        foreach ($items as $item) {
            if (!$collectionConfig->isValidItemType($item['value_type'])) {
                throw ValidationException::validationFailed(
                    'value_type',
                    sprintf(
                        'Value type "%s" is not allowed in selected block.',
                        $item['value_type'],
                    ),
                );
            }
        }
    }
}
