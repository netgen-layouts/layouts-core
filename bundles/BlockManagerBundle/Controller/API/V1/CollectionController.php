<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\ItemDraft;
use Netgen\BlockManager\API\Values\Collection\QueryDraft;
use Netgen\BlockManager\Collection\ResultGeneratorInterface;
use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\API\Values\Collection\CollectionDraft;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class CollectionController extends Controller
{
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
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\Collection\ResultGeneratorInterface $resultGenerator
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator $validator
     */
    public function __construct(
        CollectionService $collectionService,
        ResultGeneratorInterface $resultGenerator,
        CollectionValidator $validator
    ) {
        $this->collectionService = $collectionService;
        $this->resultGenerator = $resultGenerator;
        $this->validator = $validator;
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
                !empty($limit) ? (int)$limit : null
            ),
            Version::API_V1
        );
    }

    /**
     * Loads all collection items.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionItems(CollectionDraft $collection)
    {
        $items = array_map(
            function (ItemDraft $item) {
                return new VersionedValue($item, Version::API_V1);
            },
            $collection->getItems()
        );

        return new ValueArray($items);
    }

    /**
     * Loads all collection queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionQueries(CollectionDraft $collection)
    {
        $queries = array_map(
            function (QueryDraft $query) {
                return new VersionedValue($query, Version::API_V1);
            },
            $collection->getQueries()
        );

        return new ValueArray($queries);
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
     * @return \Netgen\BlockManager\Serializer\Values\View If some of the required request parameters are empty, missing or have an invalid format
     */
    public function addItem(CollectionDraft $collection, Request $request)
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
     * @param \Netgen\BlockManager\API\Values\Collection\ItemDraft $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response If some of the required request parameters are empty, missing or have an invalid format
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
     * @return \Symfony\Component\HttpFoundation\Response If some of the required request parameters are empty, missing or have an invalid format
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
     * Displays and processes query form.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If query does not support the specified form
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function queryForm(QueryDraft $query, $formName, Request $request)
    {
        $queryType = $this->getQueryType($query->getType());

        if (!$queryType->getConfig()->hasForm($formName)) {
            throw new InvalidArgumentException('form', 'Query does not support specified form.');
        }

        $updateStruct = $this->collectionService->newQueryUpdateStruct();
        $updateStruct->setParameters($query->getParameters());

        $form = $this->createForm(
            $queryType->getConfig()->getForm($formName)->getType(),
            $updateStruct,
            array(
                'queryType' => $queryType,
                'action' => $this->generateUrl(
                    'netgen_block_manager_api_v1_query_form',
                    array(
                        'queryId' => $query->getId(),
                        'formName' => $formName,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        $responseCode = Response::HTTP_OK;
        if ($request->getMethod() === Request::METHOD_POST) {
            if ($form->isValid()) {
                $query = $this->collectionService->updateQuery($query, $form->getData());
            } else {
                $responseCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            }
        }

        return new FormView($form, $query, Version::API_V1, $responseCode);
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
