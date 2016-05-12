<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\CollectionValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\CollectionValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\CollectionValidator $validator
     */
    public function __construct(
        CollectionService $collectionService,
        BlockService $blockService,
        ConfigurationInterface $configuration,
        CollectionValidator $validator
    ) {
        $this->collectionService = $collectionService;
        $this->blockService = $blockService;
        $this->configuration = $configuration;
        $this->validator = $validator;
    }

    /**
     * Loads the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function load(Collection $collection)
    {
        return new VersionedValue($collection, self::API_VERSION);
    }

    /**
     * Loads the named collections.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadNamedCollections()
    {
        $namedCollections = array_map(
            function (Collection $collection) {
                return new VersionedValue($collection, self::API_VERSION);
            },
            $this->collectionService->loadNamedCollections()
        );

        return new ValueArray($namedCollections);
    }

    /**
     * Loads all collection items.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionItems(Collection $collection)
    {
        $manualItems = array_map(
            function (Item $item) {
                return new VersionedValue($item, self::API_VERSION);
            },
            array_values($collection->getManualItems())
        );

        $overrideItems = array_map(
            function (Item $item) {
                return new VersionedValue($item, self::API_VERSION);
            },
            array_values($collection->getOverrideItems())
        );

        return new ValueArray(
            array(
                'manual_items' => $manualItems,
                'override_items' => $overrideItems,
            )
        );
    }

    /**
     * Loads all collection queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionQueries(Collection $collection)
    {
        $queries = array_map(
            function (Query $query) {
                return new VersionedValue($query, self::API_VERSION);
            },
            $collection->getQueries()
        );

        return new ValueArray($queries);
    }

    /**
     * Creates the collection.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If some of the required request parameters are empty, missing or have an invalid format
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function create(Request $request)
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(
            $request->request->get('type'),
            $request->request->get('name')
        );

        $collectionCreateStruct->status = Collection::STATUS_PUBLISHED;

        $createdCollection = $this->collectionService->createCollection(
            $collectionCreateStruct
        );

        return new VersionedValue($createdCollection, self::API_VERSION, Response::HTTP_CREATED);
    }

    /**
     * Deletes the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Collection $collection)
    {
        $this->collectionService->deleteCollection($collection, true);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads the item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $collectionItem
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadItem(Item $collectionItem)
    {
        return new VersionedValue($collectionItem, self::API_VERSION);
    }

    /**
     * Moves the item inside the collection.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Collection\Item $collectionItem
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If some of the required request parameters are empty, missing or have an invalid format
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveItem(Request $request, Item $collectionItem)
    {
        $this->collectionService->moveItem(
            $collectionItem,
            $request->request->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $collectionItem
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteItem(Item $collectionItem)
    {
        $this->collectionService->deleteItem($collectionItem);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $collectionQuery
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadQuery(Query $collectionQuery)
    {
        return new VersionedValue($collectionQuery, self::API_VERSION);
    }

    /**
     * Moves the query inside the collection.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Collection\Query $collectionQuery
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If some of the required request parameters are empty, missing or have an invalid format
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveQuery(Request $request, Query $collectionQuery)
    {
        $this->collectionService->moveQuery(
            $collectionQuery,
            $request->request->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $collectionQuery
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteQuery(Query $collectionQuery)
    {
        $this->collectionService->deleteQuery($collectionQuery);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
