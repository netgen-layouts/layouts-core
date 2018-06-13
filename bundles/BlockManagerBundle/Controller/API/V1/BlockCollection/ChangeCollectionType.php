<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\Utils\ChangeCollectionTypeValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChangeCollectionType extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\Utils\ChangeCollectionTypeValidator
     */
    private $validator;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    private $queryTypeRegistry;

    public function __construct(
        CollectionService $collectionService,
        ChangeCollectionTypeValidator $validator,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->collectionService = $collectionService;
        $this->validator = $validator;
        $this->queryTypeRegistry = $queryTypeRegistry;
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
    public function __invoke(Block $block, $collectionIdentifier, Request $request)
    {
        $requestData = $request->attributes->get('data');

        $newType = $requestData->getInt('new_type');
        $queryType = $requestData->get('query_type');

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
                $this->queryTypeRegistry->getQueryType($queryType)
            );
        }

        $this->collectionService->changeCollectionType($collection, $newType, $queryCreateStruct);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
