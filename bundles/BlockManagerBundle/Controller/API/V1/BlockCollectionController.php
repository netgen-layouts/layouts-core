<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\ResultGeneratorInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Values\Page\BlockDraft;

class BlockCollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\Collection\ResultGeneratorInterface
     */
    protected $resultGenerator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\Collection\ResultGeneratorInterface $resultGenerator
     */
    public function __construct(
        BlockService $blockService,
        CollectionService $collectionService,
        ResultGeneratorInterface $resultGenerator
    ) {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->resultGenerator = $resultGenerator;
    }

    /**
     * Loads all block draft collection references.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionReferences(BlockDraft $block)
    {
        $collectionReferences = array_map(
            function (CollectionReference $collectionReference) {
                return new VersionedValue($collectionReference, Version::API_V1);
            },
            $this->blockService->loadCollectionReferences($block)
        );

        return new ValueArray($collectionReferences);
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
            $this->resultGenerator->generateResult(
                $collectionReference->getCollection(),
                (int)$offset,
                !empty($limit) ? (int)$limit : null,
                ResultGeneratorInterface::INCLUDE_INVISIBLE_ITEMS |
                ResultGeneratorInterface::IGNORE_EXCEPTIONS
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
     *                                                                 If specified named collection is not named
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeCollectionType(CollectionReference $collectionReference, Request $request)
    {
        $newType = $request->request->get('new_type');

        $collection = $collectionReference->getCollection();

        if ($newType === Collection::TYPE_MANUAL) {
            if ($collection->getType() === Collection::TYPE_MANUAL) {
                // Noop
                return new Response(null, Response::HTTP_NO_CONTENT);
            }

            if ($collection->getType() === Collection::TYPE_NAMED) {
                $newCollection = $this->collectionService->createCollection(
                    $this->collectionService->newCollectionCreateStruct($newType)
                );
            } else {
                $newCollection = $this->collectionService->changeCollectionType($collection, $newType);
            }
        } elseif ($newType === Collection::TYPE_DYNAMIC) {
            $queryType = $this->getQueryType($request->request->get('query_type'));
            $queryCreateStruct = $this->collectionService->newQueryCreateStruct($queryType, 'default');

            if ($collection->getType() === Collection::TYPE_NAMED) {
                $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct($newType);
                $collectionCreateStruct->queryCreateStructs = array($queryCreateStruct);
                $newCollection = $this->collectionService->createCollection($collectionCreateStruct);
            } else {
                $newCollection = $this->collectionService->changeCollectionType($collection, $newType, $queryCreateStruct);
            }
        } elseif ($newType === Collection::TYPE_NAMED) {
            $newCollection = $this->collectionService->loadCollection($request->request->get('named_collection_id'));
            if ($newCollection->getType() !== Collection::TYPE_NAMED) {
                throw new InvalidArgumentException('named_collection_id', 'Specified collection is not named');
            }

            if (in_array($collection->getType(), array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC))) {
                // @TODO This deletes the reference, but we still need to keep it
                // $this->collectionService->discardDraft($collection);
            }
        } else {
            throw new InvalidArgumentException('new_type', 'Specified collection type is not valid');
        }

        $this->blockService->updateCollectionReference($collectionReference, $newCollection);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
