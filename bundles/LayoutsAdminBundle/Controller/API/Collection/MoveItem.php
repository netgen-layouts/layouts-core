<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class MoveItem extends AbstractController
{
    use ValidatorTrait;

    public function __construct(
        private CollectionService $collectionService,
    ) {}

    /**
     * Moves the item inside the collection.
     */
    public function __invoke(Item $item, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $requestData = $request->attributes->get('data');
        $this->validateRequestData($requestData);

        $this->collectionService->moveItem(
            $item,
            $requestData->getInt('position'),
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
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
