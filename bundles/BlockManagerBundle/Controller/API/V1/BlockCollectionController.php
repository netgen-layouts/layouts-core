<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\Result\ResultLoaderInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Serializer\Values\ValueList;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Values\Page\BlockDraft;

class BlockCollectionController extends Controller
{
    const NEW_TYPE_MANUAL = 'manual';

    const NEW_TYPE_DYNAMIC = 'dynamic';

    const NEW_TYPE_SHARED = 'shared';

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultLoaderInterface
     */
    protected $resultLoader;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\Collection\Result\ResultLoaderInterface $resultLoader
     */
    public function __construct(
        BlockService $blockService,
        CollectionService $collectionService,
        ResultLoaderInterface $resultLoader
    ) {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->resultLoader = $resultLoader;
    }

    /**
     * Loads all block draft collection references.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function loadCollectionReferences(BlockDraft $block)
    {
        $collectionReferences = array_map(
            function (CollectionReference $collectionReference) {
                return new VersionedValue($collectionReference, Version::API_V1);
            },
            $this->blockService->loadCollectionReferences($block)
        );

        return new ValueList($collectionReferences);
    }

    /**
     * Returns the collection result.
     *
     * @param \Netgen\BlockManager\API\Values\Page\CollectionReference $collectionReference
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollectionResult(CollectionReference $collectionReference, Request $request)
    {
        $offset = $request->query->get('offset', 0);
        $limit = $request->query->get('limit', null);

        return new VersionedValue(
            $this->resultLoader->load(
                $collectionReference->getCollection(),
                (int)$offset,
                !empty($limit) ? (int)$limit : null,
                ResultLoaderInterface::INCLUDE_INVISIBLE_ITEMS |
                ResultLoaderInterface::INCLUDE_INVALID_ITEMS
            ),
            Version::API_V1
        );
    }

    /**
     * Changes the collection type within the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\CollectionReference $collectionReference
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If new collection type is not valid
     *                                                                 If query type does not exist
     *                                                                 If specified shared collection is not shared
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeCollectionType(CollectionReference $collectionReference, Request $request)
    {
        $newType = $request->request->get('new_type');

        if (!in_array($newType, array(self::NEW_TYPE_MANUAL, self::NEW_TYPE_DYNAMIC, self::NEW_TYPE_SHARED), true)) {
            throw new InvalidArgumentException('new_type', 'Specified collection type is not valid');
        }

        $collection = $collectionReference->getCollection();

        if ($newType === self::NEW_TYPE_MANUAL) {
            if ($collection->getType() === Collection::TYPE_MANUAL) {
                // Noop
                return new Response(null, Response::HTTP_NO_CONTENT);
            }

            if ($collection->isShared()) {
                $newCollection = $this->collectionService->createCollection(
                    $this->collectionService->newCollectionCreateStruct(Collection::TYPE_MANUAL)
                );
            } else {
                $newCollection = $this->collectionService->changeCollectionType($collection, Collection::TYPE_MANUAL);
            }

            $this->blockService->updateCollectionReference($collectionReference, $newCollection);
        } elseif ($newType === self::NEW_TYPE_DYNAMIC) {
            $queryType = $this->getQueryType($request->request->get('query_type'));
            $queryCreateStruct = $this->collectionService->newQueryCreateStruct($queryType, 'default');

            if ($collection->isShared()) {
                $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(Collection::TYPE_DYNAMIC);
                $collectionCreateStruct->queryCreateStructs = array($queryCreateStruct);
                $newCollection = $this->collectionService->createCollection($collectionCreateStruct);
            } else {
                $newCollection = $this->collectionService->changeCollectionType($collection, Collection::TYPE_DYNAMIC, $queryCreateStruct);
            }

            $this->blockService->updateCollectionReference($collectionReference, $newCollection);
        } elseif ($newType === self::NEW_TYPE_SHARED) {
            $newCollection = $this->collectionService->loadCollection($request->request->get('shared_collection_id'));
            if (!$newCollection->isShared()) {
                throw new InvalidArgumentException('shared_collection_id', 'Specified collection is not shared');
            }

            if (in_array($collection->getType(), array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC))) {
                // Updating the reference must come before discarding the draft, since discarding the draft
                // would delete the reference itself
                $this->blockService->updateCollectionReference($collectionReference, $newCollection);
                $this->collectionService->discardDraft($collection);
            }
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
