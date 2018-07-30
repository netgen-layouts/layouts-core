<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\Utils\AddItemsValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddItems extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\Utils\AddItemsValidator
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface
     */
    private $itemDefinitionRegistry;

    public function __construct(
        CollectionService $collectionService,
        AddItemsValidator $validator,
        ItemDefinitionRegistryInterface $itemDefinitionRegistry
    ) {
        $this->collectionService = $collectionService;
        $this->validator = $validator;
        $this->itemDefinitionRegistry = $itemDefinitionRegistry;
    }

    /**
     * Adds an item inside the collection.
     */
    public function __invoke(Block $block, string $collectionIdentifier, Request $request): Response
    {
        $requestData = $request->attributes->get('data');

        $items = $requestData->get('items');

        $this->validator->validateAddItems($block, $collectionIdentifier, $items);

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
}
