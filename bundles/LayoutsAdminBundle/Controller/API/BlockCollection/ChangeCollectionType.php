<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

use function sprintf;

final class ChangeCollectionType extends AbstractController
{
    use ValidatorTrait;

    private CollectionService $collectionService;

    private QueryTypeRegistry $queryTypeRegistry;

    public function __construct(
        CollectionService $collectionService,
        QueryTypeRegistry $queryTypeRegistry
    ) {
        $this->collectionService = $collectionService;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Changes the collection type within the block.
     *
     * @throws \Netgen\Layouts\Exception\InvalidArgumentException If new collection type is not valid
     *                                                                 If query type does not exist
     */
    public function __invoke(Block $block, string $collectionIdentifier, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:edit');

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($block, $collectionIdentifier, $requestData);

        $collection = $block->getCollection($collectionIdentifier);
        $queryCreateStruct = null;

        $newType = $requestData->get('new_type');

        if ($newType === Collection::TYPE_MANUAL) {
            if (!$collection->hasQuery()) {
                // Noop
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } elseif ($newType === Collection::TYPE_DYNAMIC) {
            $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
                $this->queryTypeRegistry->getQueryType($requestData->get('query_type')),
            );
        }

        $this->collectionService->changeCollectionType($collection, $newType, $queryCreateStruct);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validates the provided parameter bag.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateRequestData(Block $block, string $collectionIdentifier, ParameterBag $data): void
    {
        $newType = $data->get('new_type');
        $queryType = $data->get('query_type');

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
                    ],
                ),
            ],
            'new_type',
        );

        $queryTypeConstraints = [
            new Constraints\Type(['type' => 'string']),
        ];

        if ($newType === Collection::TYPE_DYNAMIC) {
            $queryTypeConstraints[] = new Constraints\NotBlank();
        }

        $this->validate($queryType, $queryTypeConstraints, 'query_type');

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
                        $queryType,
                    ),
                );
            }
        } elseif ($newType === Collection::TYPE_MANUAL) {
            if ($collectionConfig->getValidItemTypes() === []) {
                throw ValidationException::validationFailed(
                    'new_type',
                    'Selected block does not allow manual collections.',
                );
            }
        }
    }
}
