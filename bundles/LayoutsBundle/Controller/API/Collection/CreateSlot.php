<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class CreateSlot extends AbstractController
{
    use ValidatorTrait;

    /**
     * @var \Netgen\Layouts\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Creates a slot in the provided collection.
     */
    public function __invoke(Collection $collection, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $this->collectionService->addSlot(
            $collection,
            $this->collectionService->newSlotCreateStruct(),
            $requestData->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validates the provided parameter bag.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateRequestData(ParameterBag $data): void
    {
        $this->validate(
            $data->get('position'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'integer']),
            ],
            'position'
        );
    }
}
