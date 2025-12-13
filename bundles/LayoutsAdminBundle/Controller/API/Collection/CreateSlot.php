<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class CreateSlot extends AbstractController
{
    use ValidatorTrait;

    public function __construct(
        private CollectionService $collectionService,
    ) {}

    /**
     * Creates a slot in the provided collection.
     */
    public function __invoke(Collection $collection, Request $request): Value
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $createdSlot = $this->collectionService->addSlot(
            $collection,
            $this->collectionService->newSlotCreateStruct(),
            $requestData->getInt('position'),
        );

        return new Value($createdSlot, Response::HTTP_CREATED);
    }

    /**
     * Validates the provided input bag.
     *
     * @param \Symfony\Component\HttpFoundation\InputBag<int|string> $data
     */
    private function validateRequestData(InputBag $data): void
    {
        $this->validate(
            $data->get('position'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(type: 'int'),
            ],
            'position',
        );
    }
}
