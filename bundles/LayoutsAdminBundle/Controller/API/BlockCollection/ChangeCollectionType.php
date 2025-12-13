<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\CollectionType;
use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

use function sprintf;

final class ChangeCollectionType extends AbstractController
{
    use ValidatorTrait;

    public function __construct(
        private CollectionService $collectionService,
        private QueryTypeRegistry $queryTypeRegistry,
    ) {}

    /**
     * Changes the collection type within the block.
     */
    public function __invoke(Block $block, string $collectionIdentifier, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:edit');

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($block, $collectionIdentifier, $requestData);

        $collection = $block->getCollection($collectionIdentifier);
        $queryCreateStruct = null;

        $newType = CollectionType::from($requestData->getString('new_type'));

        if ($newType === CollectionType::Manual) {
            if (!$collection->hasQuery) {
                // Noop
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } elseif ($newType === CollectionType::Dynamic) {
            $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
                $this->queryTypeRegistry->getQueryType($requestData->getString('query_type')),
            );
        }

        $this->collectionService->changeCollectionType($collection, $newType, $queryCreateStruct);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validates the provided input bag.
     *
     * @param \Symfony\Component\HttpFoundation\InputBag<int|string> $data
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateRequestData(Block $block, string $collectionIdentifier, InputBag $data): void
    {
        $newType = CollectionType::from($data->getString('new_type'));

        $queryTypeConstraints = [
            new Constraints\Type(type: 'string'),
        ];

        if ($newType === CollectionType::Dynamic) {
            $queryTypeConstraints[] = new Constraints\NotBlank();
        }

        $this->validate($data->get('query_type'), $queryTypeConstraints, 'query_type');

        $blockDefinition = $block->definition;
        if (!$blockDefinition->hasCollection($collectionIdentifier)) {
            return;
        }

        $collectionConfig = $blockDefinition->getCollection($collectionIdentifier);

        if ($newType === CollectionType::Dynamic) {
            $queryType = $data->getString('query_type');

            if (!$collectionConfig->isValidQueryType($queryType)) {
                throw ValidationException::validationFailed(
                    'new_type',
                    sprintf(
                        'Query type "%s" is not allowed in selected block.',
                        $queryType,
                    ),
                );
            }
        } elseif ($newType === CollectionType::Manual) {
            if ($collectionConfig->validItemTypes === []) {
                throw ValidationException::validationFailed(
                    'new_type',
                    'Selected block does not allow manual collections.',
                );
            }
        }
    }
}
