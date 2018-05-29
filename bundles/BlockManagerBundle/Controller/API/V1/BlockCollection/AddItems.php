<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Block\Block;
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

    public function __construct(CollectionService $collectionService, AddItemsValidator $validator)
    {
        $this->collectionService = $collectionService;
        $this->validator = $validator;
    }

    /**
     * Adds an item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, $collectionIdentifier, Request $request)
    {
        $requestData = $request->attributes->get('data');

        $items = $requestData->get('items');

        $this->validator->validateAddItems($block, $collectionIdentifier, $items);

        $this->collectionService->transaction(
            function () use ($block, $collectionIdentifier, $items) {
                foreach ($items as $item) {
                    $itemDefinition = $this->getItemDefinition($item['value_type']);

                    $itemCreateStruct = $this->collectionService->newItemCreateStruct(
                        $itemDefinition,
                        $item['type'],
                        $item['value']
                    );

                    $this->collectionService->addItem(
                        $block->getCollection($collectionIdentifier),
                        $itemCreateStruct,
                        isset($item['position']) ? $item['position'] : null
                    );
                }
            }
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
