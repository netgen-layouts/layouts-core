<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Values\Collection\ItemDraft;
use Netgen\BlockManager\API\Values\Collection\QueryDraft;
use Netgen\BlockManager\Collection\ResultGeneratorInterface;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Serializer\Values\ValueList;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\API\Values\Collection\CollectionDraft;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Exception;

class CollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Repository
     */
    protected $repository;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\Collection\ResultGeneratorInterface
     */
    protected $resultGenerator;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Repository $repository
     * @param \Netgen\BlockManager\Collection\ResultGeneratorInterface $resultGenerator
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator $validator
     */
    public function __construct(
        Repository $repository,
        ResultGeneratorInterface $resultGenerator,
        CollectionValidator $validator
    ) {
        $this->repository = $repository;
        $this->resultGenerator = $resultGenerator;
        $this->validator = $validator;

        $this->collectionService = $repository->getCollectionService();
    }

    /**
     * Loads the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollection(CollectionDraft $collection)
    {
        return new VersionedValue($collection, Version::API_V1);
    }

    /**
     * Returns the collection result.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDRaft $collection
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollectionResult(CollectionDraft $collection, Request $request)
    {
        $offset = $request->query->get('offset', 0);
        $limit = $request->query->get('limit', null);

        return new VersionedValue(
            $this->resultGenerator->generateResult(
                $collection,
                (int)$offset,
                !empty($limit) ? (int)$limit : null,
                ResultGeneratorInterface::INCLUDE_INVISIBLE_ITEMS |
                ResultGeneratorInterface::INCLUDE_INVALID_ITEMS |
                ResultGeneratorInterface::IGNORE_EXCEPTIONS
            ),
            Version::API_V1
        );
    }

    /**
     * Loads all collection items.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function loadCollectionItems(CollectionDraft $collection)
    {
        $items = array_map(
            function (ItemDraft $item) {
                return new VersionedValue($item, Version::API_V1);
            },
            $collection->getItems()
        );

        return new ValueList($items);
    }

    /**
     * Loads all collection queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function loadCollectionQueries(CollectionDraft $collection)
    {
        $queries = array_map(
            function (QueryDraft $query) {
                return new VersionedValue($query, Version::API_V1);
            },
            $collection->getQueries()
        );

        return new ValueList($queries);
    }

    /**
     * Loads the item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\ItemDraft $item
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadItem(ItemDraft $item)
    {
        return new VersionedValue($item, Version::API_V1);
    }

    /**
     * Adds an item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If some of the required parameters in request body are empty, missing or have an invalid format
     * @throws \Netgen\BlockManager\Exception\BadStateException If items could not be created
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addItems(CollectionDraft $collection, Request $request)
    {
        $this->validator->validateAddItems($request);

        $items = $request->request->get('items');

        $this->repository->beginTransaction();

        try {
            foreach ($items as $item) {
                $itemCreateStruct = $this->collectionService->newItemCreateStruct(
                    $item['type'],
                    $item['value_id'],
                    $item['value_type']
                );

                $this->collectionService->addItem(
                    $collection,
                    $itemCreateStruct,
                    isset($item['position']) ? $item['position'] : null
                );
            }

            $this->repository->commitTransaction();
        } catch (InvalidArgumentException $e) {
            $this->repository->rollbackTransaction();

            throw $e;
        } catch (Exception $e) {
            $this->repository->rollbackTransaction();

            throw new BadStateException('items', $e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * Moves the item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\ItemDraft $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveItem(ItemDraft $item, Request $request)
    {
        $this->collectionService->moveItem(
            $item,
            $request->request->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\ItemDraft $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteItem(ItemDraft $item)
    {
        $this->collectionService->deleteItem($item);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadQuery(QueryDraft $query)
    {
        return new VersionedValue($query, Version::API_V1);
    }

    /**
     * Moves the query inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveQuery(QueryDraft $query, Request $request)
    {
        $this->collectionService->moveQuery(
            $query,
            $request->request->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteQuery(QueryDraft $query)
    {
        $this->collectionService->deleteQuery($query);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
