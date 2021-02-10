<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use Symfony\Component\HttpFoundation\Response;

final class DeleteItem extends AbstractController
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Deletes the item.
     */
    public function __invoke(Item $item): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $this->collectionService->deleteItem($item);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
