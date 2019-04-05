<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Validator\ValidatorTrait;
use Netgen\Bundle\BlockManagerBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class AddItems extends AbstractController
{
    use ValidatorTrait;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface
     */
    private $itemDefinitionRegistry;

    public function __construct(
        CollectionService $collectionService,
        ItemDefinitionRegistryInterface $itemDefinitionRegistry
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

        $items = $request->attributes->get('data')->get('items');

        $this->validateAddItems($block, $collectionIdentifier, $items);

        $this->collectionService->transaction(
            function () use ($block, $collectionIdentifier, $items): void {
                foreach ($items as $item) {
                    $itemCreateStruct = $this->collectionService->newItemCreateStruct(
                        $this->itemDefinitionRegistry->getItemDefinition($item['value_type']),
                        $item['value']
                    );

                    $this->collectionService->addItem(
                        $block->getCollection($collectionIdentifier),
                        $itemCreateStruct,
                        $item['position'] ?? null
                    );
                }
            }
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validates item creation parameters from the request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param mixed $items
     */
    private function validateAddItems(Block $block, string $collectionIdentifier, $items): void
    {
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
                                        ]
                                    ),
                                ],
                            ]
                        ),
                    ]
                ),
            ],
            'items'
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
                        $item['value_type']
                    )
                );
            }
        }
    }
}
