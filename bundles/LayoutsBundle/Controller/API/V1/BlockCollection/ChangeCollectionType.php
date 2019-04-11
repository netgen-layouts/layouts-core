<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\BlockCollection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Validator\ValidatorTrait;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class ChangeCollectionType extends AbstractController
{
    use ValidatorTrait;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    private $queryTypeRegistry;

    public function __construct(
        CollectionService $collectionService,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->collectionService = $collectionService;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Changes the collection type within the block.
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If new collection type is not valid
     *                                                                 If query type does not exist
     */
    public function __invoke(Block $block, string $collectionIdentifier, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:edit');

        $requestData = $request->attributes->get('data');

        $newType = $requestData->getInt('new_type');
        $queryType = $requestData->get('query_type', '');

        $this->validateChangeCollectionType($block, $collectionIdentifier, $newType, $queryType);

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

    /**
     * Validates block creation parameters from the request.
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    private function validateChangeCollectionType(Block $block, string $collectionIdentifier, int $newType, string $queryType): void
    {
        $this->validate(
            $newType,
            [
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    [
                        'choices' => [
                            Collection::TYPE_MANUAL,
                            Collection::TYPE_DYNAMIC,
                        ],
                        'strict' => true,
                    ]
                ),
            ],
            'new_type'
        );

        $blockDefinition = $block->getDefinition();
        if (!$blockDefinition->hasCollection($collectionIdentifier)) {
            return;
        }

        $collectionConfig = $blockDefinition->getCollection($collectionIdentifier);

        if ($newType === Collection::TYPE_DYNAMIC) {
            if (!$collectionConfig->isValidQueryType($queryType)) {
                throw ValidationException::validationFailed(
                    'new_type',
                    sprintf(
                        'Query type "%s" is not allowed in selected block.',
                        $queryType
                    )
                );
            }
        } elseif ($newType === Collection::TYPE_MANUAL) {
            if ($collectionConfig->getValidItemTypes() === []) {
                throw ValidationException::validationFailed(
                    'new_type',
                    'Selected block does not allow manual collections.'
                );
            }
        }
    }
}
