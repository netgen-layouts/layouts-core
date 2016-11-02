<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\Result\ResultLoaderInterface;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @var \Netgen\BlockManager\Collection\Result\ResultLoaderInterface
     */
    protected $resultLoader;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Repository $repository
     * @param \Netgen\BlockManager\Collection\Result\ResultLoaderInterface $resultLoader
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator $validator
     */
    public function __construct(
        Repository $repository,
        ResultLoaderInterface $resultLoader,
        CollectionValidator $validator
    ) {
        $this->repository = $repository;
        $this->resultLoader = $resultLoader;
        $this->validator = $validator;

        $this->collectionService = $repository->getCollectionService();
    }

    /**
     * Loads the collection.
     *
     * @param int|string $collectionId
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollection($collectionId)
    {
        $collection = $this->collectionService->loadCollectionDraft($collectionId);

        return new VersionedValue($collection, Version::API_V1);
    }

    /**
     * Returns the collection result.
     *
     * @param int|string $collectionId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollectionResult($collectionId, Request $request)
    {
        $offset = $request->query->get('offset', 0);
        $limit = $request->query->get('limit', null);

        return new VersionedValue(
            $this->resultLoader->load(
                $this->collectionService->loadCollectionDraft($collectionId),
                (int)$offset,
                !empty($limit) ? (int)$limit : null,
                ResultLoaderInterface::INCLUDE_INVISIBLE_ITEMS |
                ResultLoaderInterface::INCLUDE_INVALID_ITEMS
            ),
            Version::API_V1
        );
    }

    /**
     * Loads all collection items.
     *
     * @param int|string $collectionId
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function loadCollectionItems($collectionId)
    {
        $collection = $this->collectionService->loadCollectionDraft($collectionId);

        $items = array_map(
            function (Item $item) {
                return new VersionedValue($item, Version::API_V1);
            },
            $collection->getItems()
        );

        return new Value($items);
    }

    /**
     * Loads all collection queries.
     *
     * @param int|string $collectionId
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function loadCollectionQueries($collectionId)
    {
        $collection = $this->collectionService->loadCollectionDraft($collectionId);

        $queries = array_map(
            function (Query $query) {
                return new VersionedValue($query, Version::API_V1);
            },
            $collection->getQueries()
        );

        return new Value($queries);
    }

    /**
     * Loads the item.
     *
     * @param int|string $itemId
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadItem($itemId)
    {
        $item = $this->collectionService->loadItemDraft($itemId);

        return new VersionedValue($item, Version::API_V1);
    }

    /**
     * Adds an item inside the collection.
     *
     * @param int|string $collectionId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addItems($collectionId, Request $request)
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
                    $this->collectionService->loadCollectionDraft($collectionId),
                    $itemCreateStruct,
                    isset($item['position']) ? $item['position'] : null
                );
            }

            $this->repository->commitTransaction();

            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            $this->repository->rollbackTransaction();

            throw $e;
        }
    }

    /**
     * Moves the item inside the collection.
     *
     * @param int|string $itemId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveItem($itemId, Request $request)
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft($itemId),
            $request->request->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the item.
     *
     * @param int|string $itemId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteItem($itemId)
    {
        $this->collectionService->deleteItem(
            $this->collectionService->loadItemDraft($itemId)
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads the query.
     *
     * @param int|string $queryId
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadQuery($queryId)
    {
        $query = $this->collectionService->loadQueryDraft($queryId);

        return new VersionedValue($query, Version::API_V1);
    }

    /**
     * Updates the query.
     *
     * @param int|string $queryId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If some of the parameters do not exist in the query
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function updateQuery($queryId, Request $request)
    {
        $query = $this->collectionService->loadQueryDraft($queryId);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct();
        $queryUpdateStruct->identifier = $request->request->get('identifier');

        $parameters = $request->request->get('parameters');
        if (is_array($request->request->get('parameters'))) {
            foreach ($parameters as $parameterName => $parameterValue) {
                if (!$query->hasParameter($parameterName)) {
                    throw new BadStateException(
                        'parameters[' . $parameterName . ']',
                        'Parameter does not exist in query.'
                    );
                }

                $parameterType = $query->getParameter($parameterName)->getParameterType();
                $queryUpdateStruct->setParameter(
                    $parameterName,
                    $parameterType->toValue($parameterValue)
                );
            }
        }

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        return new VersionedValue($updatedQuery, Version::API_V1);
    }

    /**
     * Moves the query inside the collection.
     *
     * @param int|string $queryId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveQuery($queryId, Request $request)
    {
        $this->collectionService->moveQuery(
            $this->collectionService->loadQueryDraft($queryId),
            $request->request->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the query.
     *
     * @param int|string $queryId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteQuery($queryId)
    {
        $this->collectionService->deleteQuery(
            $this->collectionService->loadQueryDraft($queryId)
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
