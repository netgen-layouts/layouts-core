<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Collection\Result\ResultLoaderInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockCollectionValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockCollectionValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultLoaderInterface
     */
    protected $resultLoader;

    /**
     * @var int
     */
    protected $maxLimit;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockCollectionValidator $validator
     * @param \Netgen\BlockManager\Collection\Result\ResultLoaderInterface $resultLoader
     * @param int $maxLimit
     */
    public function __construct(
        BlockService $blockService,
        CollectionService $collectionService,
        BlockCollectionValidator $validator,
        ResultLoaderInterface $resultLoader,
        $maxLimit
    ) {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->validator = $validator;
        $this->resultLoader = $resultLoader;
        $this->maxLimit = $maxLimit;
    }

    /**
     * Loads the collection reference.
     *
     * @param \Netgen\BlockManager\API\Values\Page\CollectionReference $collectionReference
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function load(CollectionReference $collectionReference)
    {
        return new VersionedValue($collectionReference, Version::API_V1);
    }

    /**
     * Loads all block draft collection references.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function loadCollectionReferences(Block $block)
    {
        $collectionReferences = array_map(
            function (CollectionReference $collectionReference) {
                return new VersionedValue($collectionReference, Version::API_V1);
            },
            $this->blockService->loadCollectionReferences($block)
        );

        return new Value($collectionReferences);
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
        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');

        $this->validator->validateOffsetAndLimit($offset, $limit);

        if (empty($limit) || $limit > $this->maxLimit) {
            $limit = $this->maxLimit;
        }

        return new VersionedValue(
            $this->resultLoader->load(
                $collectionReference->getCollection(),
                (int) $offset,
                (int) $limit,
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
        $this->validator->validateChangeCollectionType($request);

        $newType = $request->request->get('new_type');
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
                throw new InvalidArgumentException(
                    'shared_collection_id',
                    sprintf(
                        'Collection with ID "%s" is not shared.',
                        $newCollection->getId()
                    )
                );
            }

            if (in_array($collection->getType(), array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC), true)) {
                // Updating the reference must come before discarding the draft, since discarding the draft
                // would delete the reference itself
                $this->blockService->updateCollectionReference($collectionReference, $newCollection);
                $this->collectionService->discardDraft($collection);
            }
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
