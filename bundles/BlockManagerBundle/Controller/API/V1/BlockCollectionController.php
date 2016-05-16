<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Exception\InvalidArgumentException;

class BlockCollectionController extends Controller
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
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator $validator
     */
    public function __construct(
        CollectionService $collectionService,
        BlockService $blockService,
        CollectionValidator $validator
    ) {
        $this->collectionService = $collectionService;
        $this->blockService = $blockService;
        $this->validator = $validator;
    }

    /**
     * Loads all block collections.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollections(Block $block)
    {
        $collections = array_map(
            function (Collection $collection) {
                return new VersionedValue($collection, Version::API_V1);
            },
            $this->loadBlockCollections($block, array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC))
        );

        return new ValueArray($collections);
    }

    /**
     * Loads all named collections attached to block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadNamedCollections(Block $block)
    {
        $collections = array_map(
            function (Collection $collection) {
                return new VersionedValue($collection, Version::API_V1);
            },
            $this->loadBlockCollections($block, array(Collection::TYPE_NAMED))
        );

        return new ValueArray($collections);
    }

    /**
     * Loads the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollection(Block $block, Collection $collection)
    {
        return new VersionedValue($collection, Version::API_V1);
    }

    /**
     * Loads all collection items.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionItems(Block $block, Collection $collection)
    {
        $manualItems = array_map(
            function (Item $item) {
                return new VersionedValue($item, Version::API_V1);
            },
            array_values($collection->getManualItems())
        );

        $overrideItems = array_map(
            function (Item $item) {
                return new VersionedValue($item, Version::API_V1);
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
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionQueries(Block $block, Collection $collection)
    {
        $queries = array_map(
            function (Query $query) {
                return new VersionedValue($query, Version::API_V1);
            },
            $collection->getQueries()
        );

        return new ValueArray($queries);
    }

    /**
     * Loads the item.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadItem(Block $block, Collection $collection, Item $item)
    {
        return new VersionedValue($item, Version::API_V1);
    }

    /**
     * Adds an item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View If some of the required request parameters are empty, missing or have an invalid format
     */
    public function addItem(Block $block, Collection $collection, Request $request)
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(
            $request->request->get('type'),
            $request->request->get('value_id'),
            $request->request->get('value_type')
        );

        $createdItem = $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            $request->request->get('position')
        );

        return new VersionedValue($createdItem, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Moves the item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response If some of the required request parameters are empty, missing or have an invalid format
     */
    public function moveItem(Block $block, Collection $collection, Item $item, Request $request)
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
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteItem(Block $block, Collection $collection, Item $item)
    {
        $this->collectionService->deleteItem($item);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads the query.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadQuery(Block $block, Collection $collection, Query $query)
    {
        return new VersionedValue($query, Version::API_V1);
    }

    /**
     * Displays and processes query edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If form was not submitted
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function editQuery(Query $query, Request $request)
    {
        $queryType = $this->getQueryType($query->getType());

        $updateStruct = $this->collectionService->newQueryUpdateStruct();
        $updateStruct->setParameters($query->getParameters());

        $form = $this->createForm(
            $queryType->getConfiguration()->getForm('edit'),
            $updateStruct,
            array(
                'queryType' => $queryType,
                'method' => Request::METHOD_PATCH,
            )
        );

        $form->handleRequest($request);

        if ($request->getMethod() === Request::METHOD_PATCH) {
            if (!$form->isSubmitted()) {
                throw new InvalidArgumentException('form', 'Form is not submitted.');
            }

            if (!$form->isValid()) {
                return new FormView($form, $query, Version::API_V1, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $query = $this->collectionService->updateQuery(
                $query,
                $form->getData()
            );
        }

        return new FormView($form, $query, Version::API_V1);
    }

    /**
     * Updates the block to change the collection type it has.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function changeCollectionType(Block $block, Request $request)
    {
    }

    /**
     * Loads all block collections.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $types
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    protected function loadBlockCollections(Block $block, array $types = array())
    {
        $collections = array();
        $collectionReferences = $this->blockService->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionService->loadCollection(
                $collectionReference->getCollectionId(),
                $collectionReference->getCollectionStatus()
            );

            if (in_array($collection->getType(), $types)) {
                $collections[] = $collection;
            }
        }

        return $collections;
    }
}
