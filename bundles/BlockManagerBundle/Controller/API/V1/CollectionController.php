<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Exception;
use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Values\Collection\Collection;
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
     * @var int
     */
    protected $maxLimit;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Repository $repository
     * @param \Netgen\BlockManager\Collection\Result\ResultLoaderInterface $resultLoader
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator $validator
     * @param int $maxLimit
     */
    public function __construct(
        Repository $repository,
        ResultLoaderInterface $resultLoader,
        CollectionValidator $validator,
        $maxLimit
    ) {
        $this->repository = $repository;
        $this->resultLoader = $resultLoader;
        $this->validator = $validator;
        $this->maxLimit = $maxLimit;

        $this->collectionService = $repository->getCollectionService();
    }

    /**
     * Loads the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollection(Collection $collection)
    {
        return new VersionedValue($collection, Version::API_V1);
    }

    /**
     * Returns the collection result.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollectionResult(Collection $collection, Request $request)
    {
        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');

        $this->validator->validateOffsetAndLimit($offset, $limit);

        if (empty($limit) || $limit > $this->maxLimit) {
            $limit = $this->maxLimit;
        }

        return new VersionedValue(
            $this->resultLoader->load(
                $collection,
                (int) $offset,
                (int) $limit,
                ResultLoaderInterface::INCLUDE_INVISIBLE_ITEMS |
                ResultLoaderInterface::INCLUDE_INVALID_ITEMS
            ),
            Version::API_V1
        );
    }

    /**
     * Loads all collection items.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function loadCollectionItems(Collection $collection)
    {
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
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function loadCollectionQueries(Collection $collection)
    {
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
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadItem(Item $item)
    {
        return new VersionedValue($item, Version::API_V1);
    }

    /**
     * Adds an item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addItems(Collection $collection, Request $request)
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

            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            $this->repository->rollbackTransaction();

            throw $e;
        }
    }

    /**
     * Moves the item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveItem(Item $item, Request $request)
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
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteItem(Item $item)
    {
        $this->collectionService->deleteItem($item);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadQuery(Query $query)
    {
        return new VersionedValue($query, Version::API_V1);
    }

    /**
     * Updates the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If some of the parameters do not exist in the query
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function updateQuery(Query $query, Request $request)
    {
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
                $queryUpdateStruct->setParameterValue(
                    $parameterName,
                    $parameterType->createValueFromInput($parameterValue)
                );
            }
        }

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        return new VersionedValue($updatedQuery, Version::API_V1);
    }

    /**
     * Moves the query inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveQuery(Query $query, Request $request)
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
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteQuery(Query $query)
    {
        $this->collectionService->deleteQuery($query);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
