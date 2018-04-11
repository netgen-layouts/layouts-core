<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockCollectionValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BlockCollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockCollectionValidator
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory
     */
    private $pagerFactory;

    public function __construct(
        BlockService $blockService,
        CollectionService $collectionService,
        BlockCollectionValidator $validator,
        PagerFactory $pagerFactory
    ) {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->validator = $validator;
        $this->pagerFactory = $pagerFactory;
    }

    /**
     * Returns the collection result.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollectionResult(Block $block, $collectionIdentifier)
    {
        $collection = $block->getCollection($collectionIdentifier);

        // In non AJAX scenarios, we're always rendering the first page of the collection
        // as specified by offset and limit in the collection itself
        $pager = $this->pagerFactory->getPager($collection, 1, null, ResultSet::INCLUDE_ALL_ITEMS);

        return new VersionedValue($pager->getCurrentPageResults(), Version::API_V1);
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
    public function addItems(Block $block, $collectionIdentifier, Request $request)
    {
        $items = $request->request->get('items');

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

    /**
     * Changes the collection type within the block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If new collection type is not valid
     *                                                                 If query type does not exist
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeCollectionType(Block $block, $collectionIdentifier, Request $request)
    {
        $newType = (int) $request->request->get('new_type');
        $queryType = $request->request->get('query_type');

        $this->validator->validateChangeCollectionType($block, $collectionIdentifier, $newType, $queryType);

        $collection = $block->getCollection($collectionIdentifier);
        $queryCreateStruct = null;

        if ($newType === Collection::TYPE_MANUAL) {
            if (!$collection->hasQuery()) {
                // Noop
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } elseif ($newType === Collection::TYPE_DYNAMIC) {
            $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
                $this->getQueryType($queryType)
            );
        }

        $this->collectionService->changeCollectionType($collection, $newType, $queryCreateStruct);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');
    }
}
